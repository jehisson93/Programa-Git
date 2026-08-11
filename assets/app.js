'use strict';

// -----------------------------------------------------------------------------
// Comunicación común con la API PHP
// -----------------------------------------------------------------------------

/** Envía una solicitud a PHP, convierte la respuesta JSON y centraliza errores. */
async function apiRequest(url, options = {}) {
  const response = await fetch(url, {
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
    ...options
  });
  const data = await response.json().catch(() => ({ message: 'Respuesta inválida del servidor.' }));

  // Una respuesta 401 indica que la sesión terminó y obliga a ingresar de nuevo.
  if (response.status === 401 && document.body.dataset.page !== 'login') {
    window.location.href = 'index.php';
    throw new Error('Sesión finalizada.');
  }
  if (!response.ok) throw new Error(data.message || 'No fue posible completar la operación.');
  return data;
}

function formToObject(form) {
  // FormData lee los controles que tengan atributo name.
  return Object.fromEntries(new FormData(form).entries());
}

/** Muestra mensajes uniformes de éxito o error debajo de los formularios. */
function showMessage(id, text, type = 'success') {
  const element = document.getElementById(id);
  if (!element) return;
  element.textContent = text;
  element.className = `message ${type}`;
  element.hidden = false;
}

function hideMessage(id) {
  const element = document.getElementById(id);
  if (element) element.hidden = true;
}

function escapeHtml(value) {
  // textContent evita que un dato proveniente de MySQL se interprete como HTML.
  const element = document.createElement('div');
  element.textContent = value ?? '';
  return element.innerHTML;
}

function formatCurrency(value) {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency', currency: 'COP', maximumFractionDigits: 0
  }).format(Number(value));
}

function formatDate(value) {
  return value ? new Date(value.replace(' ', 'T')).toLocaleString('es-CO') : '';
}

async function loadCatalogs() {
  // Categorías y productos se recuperan una sola vez al abrir cada formulario.
  return apiRequest('api/catalogos.php');
}

// -----------------------------------------------------------------------------
// Funciones reutilizables para llenar listas desplegables
// -----------------------------------------------------------------------------

function fillCategories(select, categories, includeAll = false) {
  if (!select) return;
  const first = includeAll ? '<option value="">Todas</option>' : '<option value="">Seleccionar</option>';
  select.innerHTML = first + categories.map(category =>
    `<option value="${category.id_categoria}">${escapeHtml(category.nombre)}</option>`
  ).join('');
}

function fillProducts(select, products) {
  if (!select) return;
  select.innerHTML = '<option value="">Seleccionar</option>' + products.map(product =>
    `<option value="${product.id_producto}">${escapeHtml(product.codigo)} - ${escapeHtml(product.nombre)} (stock: ${product.stock_actual})</option>`
  ).join('');
}

// -----------------------------------------------------------------------------
// Inicio de sesión
// -----------------------------------------------------------------------------

async function initializeLogin() {
  document.getElementById('formLogin').addEventListener('submit', async event => {
    event.preventDefault();
    hideMessage('mensajeLogin');
    try {
      await apiRequest('api/auth.php', { method: 'POST', body: JSON.stringify(formToObject(event.target)) });
      window.location.href = 'dashboard.php';
    } catch (error) {
      showMessage('mensajeLogin', error.message, 'error');
    }
  });
}

// -----------------------------------------------------------------------------
// CRUD de productos: crear, consultar, actualizar y eliminar
// -----------------------------------------------------------------------------

async function initializeNewProduct() {
  try {
    const catalogs = await loadCatalogs();
    fillCategories(document.getElementById('id_categoria'), catalogs.categorias);
  } catch (error) {
    showMessage('mensajeProducto', error.message, 'error');
  }

  document.getElementById('formProducto').addEventListener('submit', async event => {
    event.preventDefault();
    try {
      const result = await apiRequest('api/productos.php', {
        method: 'POST', body: JSON.stringify(formToObject(event.target))
      });
      showMessage('mensajeProducto', result.message);
      event.target.reset();
    } catch (error) {
      showMessage('mensajeProducto', error.message, 'error');
    }
  });
}

async function initializeProducts() {
  const filterForm = document.getElementById('formFiltros');
  try {
    const catalogs = await loadCatalogs();
    fillCategories(document.getElementById('filtroCategoria'), catalogs.categorias, true);
    await loadProducts();
  } catch (error) {
    showMessage('mensajeProductos', error.message, 'error');
  }

  filterForm.addEventListener('submit', event => { event.preventDefault(); loadProducts(); });
  document.getElementById('btnLimpiarFiltros').addEventListener('click', () => {
    filterForm.reset();
    loadProducts();
  });
  // Un solo listener atiende todos los botones Eliminar de la tabla.
  document.getElementById('tablaProductos').addEventListener('click', async event => {
    const button = event.target.closest('[data-delete]');
    if (!button) return;
    if (!window.confirm(`¿Desea eliminar o inactivar el producto ${button.dataset.code}?`)) return;
    try {
      const result = await apiRequest(`api/productos.php?id=${button.dataset.delete}`, { method: 'DELETE' });
      showMessage('mensajeProductos', result.message);
      await loadProducts();
    } catch (error) {
      showMessage('mensajeProductos', error.message, 'error');
    }
  });
}

async function loadProducts() {
  // URLSearchParams construye una URL segura con los filtros seleccionados.
  const params = new URLSearchParams({
    busqueda: document.getElementById('buscarProducto').value.trim(),
    categoria: document.getElementById('filtroCategoria').value,
    estado: document.getElementById('filtroEstado').value
  });
  const result = await apiRequest(`api/productos.php?${params}`);
  const table = document.getElementById('tablaProductos');
  document.getElementById('cantidadResultados').textContent = `${result.productos.length} producto(s) encontrado(s)`;
  // map convierte cada producto recibido en una fila de la tabla.
  table.innerHTML = result.productos.length ? result.productos.map(product => {
    const lowStock = Number(product.stock_actual) <= Number(product.stock_minimo);
    return `<tr>
      <td>${escapeHtml(product.codigo)}</td><td>${escapeHtml(product.nombre)}</td>
      <td>${escapeHtml(product.categoria)}</td><td>${product.stock_actual}</td>
      <td>${product.stock_minimo}</td><td>${formatCurrency(product.precio)}</td>
      <td><span class="status ${lowStock ? 'low' : 'ok'}">${lowStock ? 'Stock bajo' : escapeHtml(product.estado)}</span></td>
      <td class="actions"><a href="editar-producto.php?id=${product.id_producto}">Editar</a>
      <button class="danger-link" type="button" data-delete="${product.id_producto}" data-code="${escapeHtml(product.codigo)}">Eliminar</button></td>
    </tr>`;
  }).join('') : '<tr><td colspan="8" class="empty">No se encontraron productos.</td></tr>';
}

async function initializeEditProduct() {
  // Ejemplo de URL esperada: editar-producto.php?id=3.
  const id = new URLSearchParams(window.location.search).get('id');
  if (!id) return showMessage('mensajeEditar', 'No se indicó el producto que desea modificar.', 'error');

  try {
    const [catalogs, result] = await Promise.all([loadCatalogs(), apiRequest(`api/productos.php?id=${id}`)]);
    fillCategories(document.getElementById('id_categoria'), catalogs.categorias);
    // Los ids de los campos coinciden con los nombres entregados por la API.
    Object.entries(result.producto).forEach(([key, value]) => {
      const field = document.getElementById(key);
      if (field) field.value = value ?? '';
    });
  } catch (error) {
    showMessage('mensajeEditar', error.message, 'error');
  }

  document.getElementById('formEditarProducto').addEventListener('submit', async event => {
    event.preventDefault();
    try {
      const result = await apiRequest(`api/productos.php?id=${id}`, {
        method: 'PUT', body: JSON.stringify(formToObject(event.target))
      });
      showMessage('mensajeEditar', result.message);
    } catch (error) {
      showMessage('mensajeEditar', error.message, 'error');
    }
  });
}

// -----------------------------------------------------------------------------
// Operaciones que cambian existencias
// -----------------------------------------------------------------------------

async function initializeMovement() {
  const form = document.getElementById('formMovimiento');
  try {
    const catalogs = await loadCatalogs();
    fillProducts(document.getElementById('movimientoProducto'), catalogs.productos);
  } catch (error) {
    showMessage('mensajeMovimiento', error.message, 'error');
  }
  form.addEventListener('submit', async event => {
    event.preventDefault();
    try {
      const result = await apiRequest('api/movimientos.php', { method: 'POST', body: JSON.stringify(formToObject(form)) });
      showMessage('mensajeMovimiento', result.message);
      form.reset();
      fillProducts(document.getElementById('movimientoProducto'), (await loadCatalogs()).productos);
    } catch (error) {
      showMessage('mensajeMovimiento', error.message, 'error');
    }
  });
}

async function initializeAdjustment() {
  const form = document.getElementById('formAjuste');
  try {
    const catalogs = await loadCatalogs();
    fillProducts(document.getElementById('ajusteProducto'), catalogs.productos);
  } catch (error) {
    showMessage('mensajeAjuste', error.message, 'error');
  }
  form.addEventListener('submit', async event => {
    event.preventDefault();
    try {
      const result = await apiRequest('api/ajustes.php', { method: 'POST', body: JSON.stringify(formToObject(form)) });
      showMessage('mensajeAjuste', result.message);
      form.reset();
      fillProducts(document.getElementById('ajusteProducto'), (await loadCatalogs()).productos);
    } catch (error) {
      showMessage('mensajeAjuste', error.message, 'error');
    }
  });
}

// -----------------------------------------------------------------------------
// Reportes, impresión y exportación CSV
// -----------------------------------------------------------------------------

let currentReport = { headers: [], rows: [] };

async function initializeReports() {
  document.getElementById('btnGenerarReporte').addEventListener('click', generateReport);
  document.getElementById('btnImprimir').addEventListener('click', () => window.print());
  document.getElementById('btnExportarCsv').addEventListener('click', exportCsv);
  await generateReport();
}

async function generateReport() {
  // Cada tipo usa un endpoint diferente, pero termina en la misma tabla visual.
  const type = document.getElementById('tipoReporte').value;
  try {
    let data;
    if (type === 'inventario') {
      data = (await apiRequest('api/productos.php')).productos;
      currentReport.headers = ['Código', 'Producto', 'Categoría', 'Stock', 'Mínimo', 'Estado'];
      currentReport.rows = data.map(item => [item.codigo, item.nombre, item.categoria, item.stock_actual, item.stock_minimo, item.estado]);
    } else {
      data = (await apiRequest(`api/${type}.php`))[type];
      if (type === 'movimientos') {
        currentReport.headers = ['Fecha', 'Código', 'Producto', 'Tipo', 'Cantidad', 'Usuario', 'Motivo'];
        currentReport.rows = data.map(item => [formatDate(item.fecha_hora), item.codigo, item.producto, item.tipo_movimiento, item.cantidad, item.usuario, item.motivo]);
      } else {
        currentReport.headers = ['Fecha', 'Código', 'Producto', 'Ajuste', 'Usuario', 'Motivo'];
        currentReport.rows = data.map(item => [formatDate(item.fecha_hora), item.codigo, item.producto, item.cantidad, item.usuario, item.motivo]);
      }
    }
    renderReport(type);
  } catch (error) {
    showMessage('mensajeReporte', error.message, 'error');
  }
}

function renderReport(type) {
  const body = currentReport.rows.length
    ? currentReport.rows.map(row => `<tr>${row.map(value => `<td>${escapeHtml(value)}</td>`).join('')}</tr>`).join('')
    : `<tr><td colspan="${currentReport.headers.length}" class="empty">No hay datos para mostrar.</td></tr>`;
  document.getElementById('vistaReporte').innerHTML = `<h2>Reporte: ${escapeHtml(type)}</h2>
    <p>${currentReport.rows.length} registro(s).</p><div class="table-wrap"><table><thead><tr>
    ${currentReport.headers.map(header => `<th>${escapeHtml(header)}</th>`).join('')}</tr></thead><tbody>${body}</tbody></table></div>`;
}

function exportCsv() {
  // La marca BOM (\ufeff) ayuda a que Excel reconozca correctamente las tildes.
  if (!currentReport.rows.length) return showMessage('mensajeReporte', 'No hay datos para exportar.', 'error');
  const quote = value => `"${String(value ?? '').replaceAll('"', '""')}"`;
  const content = [currentReport.headers, ...currentReport.rows].map(row => row.map(quote).join(',')).join('\n');
  const link = document.createElement('a');
  link.href = URL.createObjectURL(new Blob(['\ufeff' + content], { type: 'text/csv;charset=utf-8' }));
  link.download = 'reporte-inventario.csv';
  link.click();
  URL.revokeObjectURL(link.href);
}

document.addEventListener('DOMContentLoaded', () => {
  // Este evento se ejecuta cuando la estructura HTML ya está disponible.
  document.getElementById('btnCerrarSesion')?.addEventListener('click', async () => {
    await apiRequest('api/auth.php', { method: 'DELETE' });
    window.location.href = 'index.php';
  });

  // data-page del body decide qué funciones necesita cada página.
  const initializers = {
    login: initializeLogin,
    nuevoProducto: initializeNewProduct,
    productos: initializeProducts,
    editarProducto: initializeEditProduct,
    movimiento: initializeMovement,
    ajuste: initializeAdjustment,
    reportes: initializeReports
  };
  initializers[document.body.dataset.page]?.();
});
