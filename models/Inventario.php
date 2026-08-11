<?php

declare(strict_types=1);

/**
 * Contiene las reglas que modifican existencias.
 *
 * Movimientos y ajustes se ejecutan mediante transacciones porque registrar el
 * historial y modificar el stock forman una sola operación lógica.
 */
final class Inventario
{
    public function __construct(private PDO $connection)
    {
    }

    public function crearMovimiento(array $data, int $userId): int
    {
        // Se aceptan cantidades positivas; el tipo define si se suman o restan.
        $productId = integerValue($data, 'id_producto', 1);
        $quantity = integerValue($data, 'cantidad', 1);
        $type = textValue($data, 'tipo_movimiento', 20);
        if (!in_array($type, ['Entrada', 'Salida'], true)) {
            throw new InvalidArgumentException('El tipo de movimiento no es válido.');
        }

        // Desde aquí, todos los cambios se confirman juntos o se deshacen juntos.
        $this->connection->beginTransaction();
        try {
            $product = $this->lockProduct($productId);
            $newStock = $type === 'Entrada'
                ? (int) $product['stock_actual'] + $quantity
                : (int) $product['stock_actual'] - $quantity;
            if ($newStock < 0) {
                throw new InvalidArgumentException('No hay existencias suficientes para registrar la salida.');
            }

            $statement = $this->connection->prepare(
                'INSERT INTO movimiento
                    (id_producto, id_usuario, tipo_movimiento, fecha_hora, cantidad, motivo, observacion)
                 VALUES (:id_producto, :id_usuario, :tipo, NOW(), :cantidad, :motivo, :observacion)'
            );
            $statement->execute([
                'id_producto' => $productId,
                'id_usuario' => $userId,
                'tipo' => $type,
                'cantidad' => $quantity,
                'motivo' => textValue($data, 'motivo', 100),
                'observacion' => textValue($data, 'observacion', 255, false),
            ]);
            $this->updateStock($productId, $newStock);
            $movementId = (int) $this->connection->lastInsertId();
            // commit hace permanentes el historial y el nuevo stock.
            $this->connection->commit();
            return $movementId;
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                // rollBack evita que quede actualizado solo uno de los dos registros.
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    public function crearAjuste(array $data, int $userId): int
    {
        // A diferencia del movimiento, un ajuste acepta valores positivos o negativos.
        $productId = integerValue($data, 'id_producto', 1);
        $quantity = filter_var($data['cantidad_ajustada'] ?? null, FILTER_VALIDATE_INT);
        if ($quantity === false || $quantity === 0) {
            throw new InvalidArgumentException('La cantidad ajustada debe ser diferente de cero.');
        }

        $this->connection->beginTransaction();
        try {
            $product = $this->lockProduct($productId);
            $newStock = (int) $product['stock_actual'] + $quantity;
            if ($newStock < 0) {
                throw new InvalidArgumentException('El ajuste no puede dejar el stock en un valor negativo.');
            }

            $statement = $this->connection->prepare(
                'INSERT INTO ajuste
                    (id_producto, id_usuario, fecha_hora, cantidad_ajustada, motivo, observacion)
                 VALUES (:id_producto, :id_usuario, NOW(), :cantidad, :motivo, :observacion)'
            );
            $statement->execute([
                'id_producto' => $productId,
                'id_usuario' => $userId,
                'cantidad' => $quantity,
                'motivo' => textValue($data, 'motivo', 100),
                'observacion' => textValue($data, 'observacion', 255, false),
            ]);
            $this->updateStock($productId, $newStock);
            $adjustmentId = (int) $this->connection->lastInsertId();
            $this->connection->commit();
            return $adjustmentId;
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    public function listar(string $type): array
    {
        // Los JOIN sustituyen los identificadores por nombres legibles en los reportes.
        if ($type === 'ajustes') {
            $sql = 'SELECT a.fecha_hora, p.codigo, p.nombre AS producto,
                           a.cantidad_ajustada AS cantidad, u.nombres AS usuario, a.motivo
                    FROM ajuste a
                    INNER JOIN producto p ON p.id_producto = a.id_producto
                    INNER JOIN usuario u ON u.id_usuario = a.id_usuario
                    ORDER BY a.fecha_hora DESC';
        } else {
            $sql = 'SELECT m.fecha_hora, p.codigo, p.nombre AS producto, m.tipo_movimiento,
                           m.cantidad, u.nombres AS usuario, m.motivo
                    FROM movimiento m
                    INNER JOIN producto p ON p.id_producto = m.id_producto
                    INNER JOIN usuario u ON u.id_usuario = m.id_usuario
                    ORDER BY m.fecha_hora DESC';
        }
        return $this->connection->query($sql)->fetchAll();
    }

    private function lockProduct(int $productId): array
    {
        // FOR UPDATE evita que dos usuarios cambien simultáneamente el mismo stock.
        $statement = $this->connection->prepare(
            "SELECT stock_actual FROM producto
             WHERE id_producto = :id AND estado = 'Activo' FOR UPDATE"
        );
        $statement->execute(['id' => $productId]);
        $product = $statement->fetch();
        if (!$product) {
            throw new InvalidArgumentException('El producto no existe o está inactivo.');
        }
        return $product;
    }

    private function updateStock(int $productId, int $newStock): void
    {
        // Método privado reutilizado por movimientos y ajustes.
        $statement = $this->connection->prepare(
            'UPDATE producto SET stock_actual = :stock WHERE id_producto = :id'
        );
        $statement->execute(['stock' => $newStock, 'id' => $productId]);
    }
}
