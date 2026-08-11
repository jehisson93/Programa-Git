<?php
/**
 * Formulario reutilizable para crear y editar productos.
 *
 * Las páginas que incluyen este archivo deben definir estas variables:
 * - $formId: identificador usado por JavaScript.
 * - $messageId: lugar donde se muestran mensajes de éxito o error.
 * - $isEditing: indica si el formulario carga un producto existente.
 * - $submitLabel: texto del botón principal.
 */
$isEditing = $isEditing ?? false;
?>
<form id="<?= htmlspecialchars($formId) ?>">
  <?php if ($isEditing): ?>
    <!-- El identificador no se muestra, pero permite reconocer el registro cargado. -->
    <input id="id_producto" name="id_producto" type="hidden">
  <?php endif; ?>

  <div class="form-grid two">
    <div>
      <label<?= $isEditing ? '' : ' class="required"' ?> for="codigo">Código</label>
      <input id="codigo" name="codigo" maxlength="30" pattern="[A-Za-z0-9-]+"
             <?= $isEditing ? 'disabled' : 'required' ?>>
    </div>
    <div><label class="required" for="nombre">Nombre</label><input id="nombre" name="nombre" required maxlength="120"></div>
    <div class="field-full"><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" rows="3" maxlength="255"></textarea></div>
    <div><label class="required" for="id_categoria">Categoría</label><select id="id_categoria" name="id_categoria" required><option value="">Seleccionar</option></select></div>
    <div><label class="required" for="unidad_medida">Unidad de medida</label><input id="unidad_medida" name="unidad_medida" placeholder="Ejemplo: Unidad" required maxlength="30"></div>
    <div><label class="required" for="stock_actual">Stock actual</label><input id="stock_actual" name="stock_actual" type="number" min="0" value="0" required></div>
    <div><label class="required" for="stock_minimo">Stock mínimo</label><input id="stock_minimo" name="stock_minimo" type="number" min="0" value="0" required></div>
    <div><label class="required" for="precio">Precio unitario (COP)</label><input id="precio" name="precio" type="number" min="0" step="0.01" value="0" required></div>
    <div><label class="required" for="estado">Estado</label><select id="estado" name="estado" required><option>Activo</option><option>Inactivo</option></select></div>
  </div>

  <!-- JavaScript actualiza este párrafo según la respuesta entregada por la API. -->
  <p id="<?= htmlspecialchars($messageId) ?>" class="message" role="status" hidden></p>
  <div class="buttons">
    <button class="primary" type="submit"><?= htmlspecialchars($submitLabel) ?></button>
    <a class="btn-link secondary" href="productos.php">Cancelar</a>
  </div>
</form>
