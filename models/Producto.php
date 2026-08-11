<?php

declare(strict_types=1);

final class Producto
{
    public function __construct(private PDO $connection)
    {
    }

    public function listar(string $busqueda = '', ?int $categoria = null, string $estado = ''): array
    {
        $conditions = [];
        $parameters = [];
        if ($busqueda !== '') {
            $conditions[] = '(p.codigo LIKE :patron_codigo OR p.nombre LIKE :patron_nombre)';
            $parameters['patron_codigo'] = "%{$busqueda}%";
            $parameters['patron_nombre'] = "%{$busqueda}%";
        }
        if ($categoria !== null) {
            $conditions[] = 'p.id_categoria = :categoria';
            $parameters['categoria'] = $categoria;
        }
        if ($estado !== '') {
            $conditions[] = 'p.estado = :estado';
            $parameters['estado'] = $estado;
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $sql = 'SELECT p.id_producto, p.id_categoria, p.codigo, p.nombre, p.descripcion,
                       p.unidad_medida, p.stock_minimo, p.stock_actual, p.precio, p.estado,
                       c.nombre AS categoria
                FROM producto p
                INNER JOIN categoria c ON c.id_categoria = p.id_categoria' . $where . '
                ORDER BY p.nombre';
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT id_producto, id_categoria, codigo, nombre, descripcion, unidad_medida,
                    stock_minimo, stock_actual, precio, estado
             FROM producto WHERE id_producto = :id'
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();
        return $product ?: null;
    }

    public function crear(array $data): int
    {
        $product = $this->validate($data);
        $statement = $this->connection->prepare(
            'INSERT INTO producto
                (id_categoria, codigo, nombre, descripcion, unidad_medida,
                 stock_minimo, stock_actual, precio, estado)
             VALUES
                (:id_categoria, :codigo, :nombre, :descripcion, :unidad_medida,
                 :stock_minimo, :stock_actual, :precio, :estado)'
        );

        try {
            $statement->execute($product);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                throw new InvalidArgumentException('El código del producto ya está registrado.');
            }
            throw $exception;
        }

        return (int) $this->connection->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        if ($this->buscarPorId($id) === null) {
            throw new InvalidArgumentException('El producto no existe.');
        }

        $product = $this->validate($data, false);
        $product['id'] = $id;
        $statement = $this->connection->prepare(
            'UPDATE producto SET
                id_categoria = :id_categoria,
                nombre = :nombre,
                descripcion = :descripcion,
                unidad_medida = :unidad_medida,
                stock_minimo = :stock_minimo,
                stock_actual = :stock_actual,
                precio = :precio,
                estado = :estado
             WHERE id_producto = :id'
        );
        unset($product['codigo']);
        return $statement->execute($product);
    }

    public function eliminar(int $id): bool
    {
        if ($this->buscarPorId($id) === null) {
            throw new InvalidArgumentException('El producto no existe.');
        }

        $historyStatement = $this->connection->prepare(
            'SELECT
                (SELECT COUNT(*) FROM movimiento WHERE id_producto = :id_movimiento) +
                (SELECT COUNT(*) FROM ajuste WHERE id_producto = :id_ajuste) AS total'
        );
        $historyStatement->execute(['id_movimiento' => $id, 'id_ajuste' => $id]);
        $hasHistory = (int) $historyStatement->fetchColumn() > 0;

        if ($hasHistory) {
            $statement = $this->connection->prepare(
                "UPDATE producto SET estado = 'Inactivo' WHERE id_producto = :id"
            );
        } else {
            $statement = $this->connection->prepare(
                'DELETE FROM producto WHERE id_producto = :id'
            );
        }

        return $statement->execute(['id' => $id]);
    }

    private function validate(array $data, bool $includeCode = true): array
    {
        $state = textValue($data, 'estado', 20);
        if (!in_array($state, ['Activo', 'Inactivo'], true)) {
            throw new InvalidArgumentException('El estado seleccionado no es válido.');
        }

        $product = [
            'id_categoria' => integerValue($data, 'id_categoria', 1),
            'nombre' => textValue($data, 'nombre', 120),
            'descripcion' => textValue($data, 'descripcion', 255, false),
            'unidad_medida' => textValue($data, 'unidad_medida', 30),
            'stock_minimo' => integerValue($data, 'stock_minimo'),
            'stock_actual' => integerValue($data, 'stock_actual'),
            'precio' => round((float) ($data['precio'] ?? 0), 2),
            'estado' => $state,
        ];

        if ($product['precio'] < 0) {
            throw new InvalidArgumentException('El precio no puede ser negativo.');
        }

        if ($includeCode) {
            $code = strtoupper(preg_replace('/\s+/', '', textValue($data, 'codigo', 30)));
            if (!preg_match('/^[A-Z0-9-]+$/', $code)) {
                throw new InvalidArgumentException('El código solo puede contener letras, números y guiones.');
            }
            $product = ['codigo' => $code] + $product;
        }

        return $product;
    }
}
