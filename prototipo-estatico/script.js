// Claves usadas para guardar la información del prototipo en el navegador.
const CLAVES = {
  productos: 'sgi_productos',
  movimientos: 'sgi_movimientos',
  ajustes: 'sgi_ajustes'
};

const PRODUCTOS_INICIALES = [
  { codigo: 'PROD001', nombre: 'Laptop Dell XPS 15', descripcion: 'Equipo portátil de alto rendimiento.', categoria: 'Electrónica', unidad: 'Unidad', stockActual: 8, stockMinimo: 5, precio: 1500, estado: 'Activo' },
  { codigo: 'PROD002', nombre: 'Resmas de papel A4', descripcion: 'Papel blanco tamaño A4.', categoria: 'Papelería', unidad: 'Paquete', stockActual: 3, stockMinimo: 10, precio: 5, estado: 'Activo' },
  { codigo: 'PROD003', nombre: 'Silla ergonómica', descripcion: 'Silla ajustable para oficina.', categoria: 'Mobiliario', unidad: 'Unidad', stockActual: 12, stockMinimo: 5, precio: 250, estado: 'Activo' },
  { codigo: 'PROD004', nombre: 'Monitor curvo 27 pulgadas', descripcion: 'Monitor para estación de trabajo.', categoria: 'Electrónica', unidad: 'Unidad', stockActual: 18, stockMinimo: 10, precio: 300, estado: 'Activo' }
];

// Lee una colección. Si todavía no existe, guarda y devuelve los datos iniciales.
function leerColeccion(clave, datosIniciales = []) {
  const contenido = localStorage.getItem(clave);

  if (!contenido) {
    localStorage.setItem(clave, JSON.stringify(datosIniciales));
    return [...datosIniciales];
  }

  try {
    return JSON.parse(contenido);
  } catch (error) {
    console.error('No fue posible leer la información guardada:', error);
    return [...datosIniciales];
  }
}

function guardarColeccion(clave, datos) {
  localStorage.setItem(clave, JSON.stringify(datos));
}

function obtenerProductos() {
  return leerColeccion(CLAVES.productos, PRODUCTOS_INICIALES);
}

function normalizarCodigo(codigo) {
  return codigo.trim().toUpperCase().replace(/\s+/g, '');
}

function escaparHtml(texto) {
  const elemento = document.createElement('div');
  elemento.textContent = texto ?? '';
  return elemento.innerHTML;
}

function mostrarMensaje(id, texto, tipo = 'success') {
  const mensaje = document.getElementById(id);
  if (!mensaje) return;

  mensaje.textContent = texto;
  mensaje.className = `message ${tipo}`;
  mensaje.hidden = false;
}

function ocultarMensaje(id) {
  const mensaje = document.getElementById(id);
  if (mensaje) mensaje.hidden = true;
}

function formatearMoneda(valor) {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0
  }).format(valor);
}

function formatearFecha(valor) {
  if (!valor) return 'Sin fecha';
  return new Date(valor).toLocaleString('es-CO');
}

function llenarSelectProductos(select, incluirTodos = false) {
  if (!select) return;

  const primeraOpcion = incluirTodos
    ? '<option value="">Todos</option>'
    : '<option value="">Seleccionar producto</option>';

  select.innerHTML = primeraOpcion + obtenerProductos()
    .filter((producto) => producto.estado === 'Activo' || incluirTodos)
    .map((producto) => `<option value="${escaparHtml(producto.codigo)}">${escaparHtml(producto.codigo)} - ${escaparHtml(producto.nombre)}</option>`)
    .join('');
}

// -------------------- Inicio de sesión --------------------
let intentosIngreso = 0;

function iniciarLogin() {
  const formulario = document.getElementById('formLogin');
  const mostrar = document.getElementById('mostrarContrasena');

  formulario?.addEventListener('submit', ingresar);
  document.getElementById('btnLimpiarLogin')?.addEventListener('click', limpiarLogin);
  mostrar?.addEventListener('change', function () {
    document.getElementById('contrasena').type = this.checked ? 'text' : 'password';
  });
}

function ingresar(evento) {
  evento.preventDefault();
  const usuario = document.getElementById('usuario').value.trim();
  const contrasena = document.getElementById('contrasena').value;
  const boton = document.getElementById('btnIngresar');

  // Credenciales demostrativas para un prototipo académico sin servidor.
  if (usuario === 'admin' && contrasena === '1234') {
    sessionStorage.setItem('sgi_usuario', usuario);
    window.location.href = 'menu-principal.html';
    return;
  }

  intentosIngreso += 1;
  const restantes = 3 - intentosIngreso;

  if (restantes > 0) {
    mostrarMensaje('mensajeError', `Credenciales incorrectas. Intentos disponibles: ${restantes}.`, 'error');
  } else {
    mostrarMensaje('mensajeError', 'Se alcanzó el límite de intentos. Use Limpiar para volver a intentar.', 'error');
    boton.disabled = true;
  }
}

function limpiarLogin() {
  document.getElementById('formLogin')?.reset();
  document.getElementById('btnIngresar').disabled = false;
  intentosIngreso = 0;
  ocultarMensaje('mensajeError');
  document.getElementById('usuario')?.focus();
}

// -------------------- Registro de usuario --------------------
function iniciarRegistroUsuario() {
  document.getElementById('formRegistroUsuario')?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    mostrarMensaje('mensajeRegistro', 'Usuario registrado correctamente en esta demostración. Ya puede volver al inicio de sesión.');
    evento.target.reset();
  });
}

// -------------------- Productos --------------------
function iniciarRegistroProducto() {
  document.getElementById('formProducto')?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    const formulario = new FormData(evento.target);
    const productos = obtenerProductos();
    const codigo = normalizarCodigo(formulario.get('codigo'));

    if (productos.some((producto) => producto.codigo === codigo)) {
      mostrarMensaje('mensajeProducto', 'El código ya está registrado. Use otro código.', 'error');
      return;
    }

    const producto = {
      codigo,
      nombre: formulario.get('nombre').trim(),
      descripcion: formulario.get('descripcion').trim(),
      categoria: formulario.get('categoria'),
      unidad: formulario.get('unidad').trim(),
      stockActual: Number(formulario.get('stockActual')),
      stockMinimo: Number(formulario.get('stockMinimo')),
      precio: Number(formulario.get('precio')),
      estado: formulario.get('estado')
    };

    productos.push(producto);
    guardarColeccion(CLAVES.productos, productos);
    mostrarMensaje('mensajeProducto', `Producto ${codigo} registrado correctamente.`);
    evento.target.reset();
  });
}

function iniciarConsulta() {
  document.getElementById('formConsulta')?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    mostrarResultadosConsulta();
  });

  document.getElementById('btnLimpiarConsulta')?.addEventListener('click', () => {
    document.getElementById('formConsulta').reset();
    mostrarResultadosConsulta();
  });

  mostrarResultadosConsulta();
}

function mostrarResultadosConsulta() {
  const texto = (document.getElementById('buscarProducto')?.value || '').trim().toLowerCase();
  const categoria = document.getElementById('filtroCategoria')?.value || '';
  const estado = document.getElementById('filtroEstado')?.value || '';
  const productos = obtenerProductos().filter((producto) => {
    const coincideTexto = producto.nombre.toLowerCase().includes(texto) || producto.codigo.toLowerCase().includes(texto);
    return coincideTexto && (!categoria || producto.categoria === categoria) && (!estado || producto.estado === estado);
  });

  const cuerpo = document.getElementById('tablaProductos');
  if (!cuerpo) return;

  cuerpo.innerHTML = productos.length
    ? productos.map((producto) => {
      const stockBajo = producto.stockActual <= producto.stockMinimo;
      return `<tr>
        <td>${escaparHtml(producto.codigo)}</td>
        <td>${escaparHtml(producto.nombre)}</td>
        <td>${escaparHtml(producto.categoria)}</td>
        <td>${producto.stockActual}</td>
        <td>${producto.stockMinimo}</td>
        <td>${formatearMoneda(producto.precio)}</td>
        <td><span class="status ${stockBajo ? 'low' : 'ok'}">${stockBajo ? 'Stock bajo' : producto.estado}</span></td>
        <td><a href="modificar.html?codigo=${encodeURIComponent(producto.codigo)}">Editar</a></td>
      </tr>`;
    }).join('')
    : '<tr><td colspan="8" class="empty">No se encontraron productos con esos filtros.</td></tr>';

  document.getElementById('cantidadResultados').textContent = `${productos.length} producto(s) encontrado(s)`;
}

function iniciarModificacionProducto() {
  const selector = document.getElementById('productoEditar');
  llenarSelectProductos(selector, true);

  const codigoUrl = new URLSearchParams(window.location.search).get('codigo');
  if (codigoUrl && [...selector.options].some((opcion) => opcion.value === codigoUrl)) {
    selector.value = codigoUrl;
  } else if (selector.options.length > 1) {
    selector.selectedIndex = 1;
  }

  selector.addEventListener('change', cargarProductoSeleccionado);
  document.getElementById('formModificarProducto')?.addEventListener('submit', actualizarProducto);
  cargarProductoSeleccionado();
}

function cargarProductoSeleccionado() {
  const codigo = document.getElementById('productoEditar').value;
  const producto = obtenerProductos().find((item) => item.codigo === codigo);
  if (!producto) return;

  const formulario = document.getElementById('formModificarProducto');
  Object.entries(producto).forEach(([campo, valor]) => {
    if (formulario.elements[campo]) formulario.elements[campo].value = valor;
  });
  ocultarMensaje('mensajeModificar');
}

function actualizarProducto(evento) {
  evento.preventDefault();
  const formulario = new FormData(evento.target);
  const codigoOriginal = document.getElementById('productoEditar').value;
  const productos = obtenerProductos();
  const indice = productos.findIndex((producto) => producto.codigo === codigoOriginal);

  if (indice === -1) {
    mostrarMensaje('mensajeModificar', 'No fue posible encontrar el producto.', 'error');
    return;
  }

  productos[indice] = {
    ...productos[indice],
    nombre: formulario.get('nombre').trim(),
    descripcion: formulario.get('descripcion').trim(),
    categoria: formulario.get('categoria'),
    unidad: formulario.get('unidad').trim(),
    stockActual: Number(formulario.get('stockActual')),
    stockMinimo: Number(formulario.get('stockMinimo')),
    precio: Number(formulario.get('precio')),
    estado: formulario.get('estado')
  };

  guardarColeccion(CLAVES.productos, productos);
  mostrarMensaje('mensajeModificar', `Producto ${codigoOriginal} actualizado correctamente.`);
}

// -------------------- Movimientos y ajustes --------------------
function iniciarMovimiento() {
  llenarSelectProductos(document.getElementById('movimientoProducto'));
  establecerFechaActual('movimientoFecha');
  document.getElementById('formMovimiento')?.addEventListener('submit', registrarMovimiento);
}

function registrarMovimiento(evento) {
  evento.preventDefault();
  const datos = new FormData(evento.target);
  const codigo = datos.get('producto');
  const cantidad = Number(datos.get('cantidad'));
  const tipo = datos.get('tipo');
  const productos = obtenerProductos();
  const producto = productos.find((item) => item.codigo === codigo);

  if (!producto) return mostrarMensaje('mensajeMovimiento', 'Seleccione un producto válido.', 'error');
  if (tipo === 'Salida' && cantidad > producto.stockActual) {
    return mostrarMensaje('mensajeMovimiento', `No hay existencias suficientes. Stock disponible: ${producto.stockActual}.`, 'error');
  }

  producto.stockActual += tipo === 'Entrada' ? cantidad : -cantidad;
  guardarColeccion(CLAVES.productos, productos);

  const movimientos = leerColeccion(CLAVES.movimientos);
  movimientos.push({
    id: Date.now(), productoCodigo: codigo, usuario: datos.get('usuario').trim(),
    tipo, fecha: datos.get('fecha'), cantidad, motivo: datos.get('motivo').trim(),
    observacion: datos.get('observacion').trim()
  });
  guardarColeccion(CLAVES.movimientos, movimientos);
  mostrarMensaje('mensajeMovimiento', `${tipo} registrada. Nuevo stock de ${codigo}: ${producto.stockActual}.`);
  evento.target.reset();
  establecerFechaActual('movimientoFecha');
}

function iniciarAjuste() {
  llenarSelectProductos(document.getElementById('ajusteProducto'));
  establecerFechaActual('ajusteFecha');
  document.getElementById('formAjuste')?.addEventListener('submit', registrarAjuste);
}

function registrarAjuste(evento) {
  evento.preventDefault();
  const datos = new FormData(evento.target);
  const codigo = datos.get('producto');
  const cantidad = Number(datos.get('cantidad'));
  const productos = obtenerProductos();
  const producto = productos.find((item) => item.codigo === codigo);

  if (!producto) return mostrarMensaje('mensajeAjuste', 'Seleccione un producto válido.', 'error');
  if (cantidad === 0 || producto.stockActual + cantidad < 0) {
    return mostrarMensaje('mensajeAjuste', 'El ajuste debe ser diferente de cero y no puede dejar un stock negativo.', 'error');
  }

  producto.stockActual += cantidad;
  guardarColeccion(CLAVES.productos, productos);

  const ajustes = leerColeccion(CLAVES.ajustes);
  ajustes.push({
    id: Date.now(), productoCodigo: codigo, usuario: datos.get('usuario').trim(),
    fecha: datos.get('fecha'), cantidad, motivo: datos.get('motivo').trim(),
    observacion: datos.get('observacion').trim()
  });
  guardarColeccion(CLAVES.ajustes, ajustes);
  mostrarMensaje('mensajeAjuste', `Ajuste registrado. Nuevo stock de ${codigo}: ${producto.stockActual}.`);
  evento.target.reset();
  establecerFechaActual('ajusteFecha');
}

function establecerFechaActual(id) {
  const campo = document.getElementById(id);
  if (!campo) return;
  const ahora = new Date();
  ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
  campo.value = ahora.toISOString().slice(0, 16);
}

// -------------------- Reportes --------------------
function iniciarReportes() {
  llenarSelectProductos(document.getElementById('reporteProducto'), true);
  document.getElementById('formReporte')?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    generarReporte();
  });
  document.getElementById('btnExportarCsv')?.addEventListener('click', exportarCsv);
  document.getElementById('btnImprimir')?.addEventListener('click', () => window.print());
  generarReporte();
}

function obtenerDatosReporte() {
  const tipo = document.getElementById('tipoReporte').value;
  const desde = document.getElementById('fechaInicio').value;
  const hasta = document.getElementById('fechaFin').value;
  const producto = document.getElementById('reporteProducto').value;

  let datos;
  if (tipo === 'Movimientos') datos = leerColeccion(CLAVES.movimientos);
  else if (tipo === 'Ajustes') datos = leerColeccion(CLAVES.ajustes);
  else datos = obtenerProductos();

  if (tipo !== 'Inventario') {
    datos = datos.filter((item) => (!producto || item.productoCodigo === producto)
      && (!desde || item.fecha.slice(0, 10) >= desde)
      && (!hasta || item.fecha.slice(0, 10) <= hasta));
  } else if (producto) {
    datos = datos.filter((item) => item.codigo === producto);
  }

  return { tipo, datos };
}

function generarReporte() {
  const { tipo, datos } = obtenerDatosReporte();
  const contenedor = document.getElementById('vistaReporte');
  if (!contenedor) return;

  let encabezados;
  let filas;
  if (tipo === 'Inventario') {
    encabezados = ['Código', 'Producto', 'Categoría', 'Stock', 'Mínimo', 'Estado'];
    filas = datos.map((item) => [item.codigo, item.nombre, item.categoria, item.stockActual, item.stockMinimo, item.estado]);
  } else if (tipo === 'Movimientos') {
    encabezados = ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'Usuario', 'Motivo'];
    filas = datos.map((item) => [formatearFecha(item.fecha), item.productoCodigo, item.tipo, item.cantidad, item.usuario, item.motivo]);
  } else {
    encabezados = ['Fecha', 'Producto', 'Ajuste', 'Usuario', 'Motivo'];
    filas = datos.map((item) => [formatearFecha(item.fecha), item.productoCodigo, item.cantidad, item.usuario, item.motivo]);
  }

  contenedor.innerHTML = `<h2>Vista previa: ${tipo}</h2>
    <p>${filas.length} registro(s).</p>
    <div class="table-wrap"><table><thead><tr>${encabezados.map((item) => `<th>${item}</th>`).join('')}</tr></thead>
    <tbody>${filas.length ? filas.map((fila) => `<tr>${fila.map((valor) => `<td>${escaparHtml(valor)}</td>`).join('')}</tr>`).join('') : `<tr><td colspan="${encabezados.length}" class="empty">No hay datos para mostrar.</td></tr>`}</tbody></table></div>`;
}

function exportarCsv() {
  const { tipo, datos } = obtenerDatosReporte();
  if (!datos.length) return mostrarMensaje('mensajeReporte', 'No hay datos para exportar.', 'error');

  const encabezados = Object.keys(datos[0]);
  const escaparCsv = (valor) => `"${String(valor ?? '').replace(/"/g, '""')}"`;
  const contenido = [encabezados, ...datos.map((item) => encabezados.map((campo) => item[campo]))]
    .map((fila) => fila.map(escaparCsv).join(','))
    .join('\n');
  const enlace = document.createElement('a');
  enlace.href = URL.createObjectURL(new Blob(['\ufeff' + contenido], { type: 'text/csv;charset=utf-8' }));
  enlace.download = `reporte-${tipo.toLowerCase()}.csv`;
  enlace.click();
  URL.revokeObjectURL(enlace.href);
  mostrarMensaje('mensajeReporte', 'Archivo CSV generado correctamente.');
}

// Ejecuta solo el código que corresponde a la página abierta.
document.addEventListener('DOMContentLoaded', () => {
  const pagina = document.body.dataset.page;
  const inicializadores = {
    login: iniciarLogin,
    registroUsuario: iniciarRegistroUsuario,
    registrarProducto: iniciarRegistroProducto,
    consultar: iniciarConsulta,
    modificar: iniciarModificacionProducto,
    movimiento: iniciarMovimiento,
    ajuste: iniciarAjuste,
    reportes: iniciarReportes
  };

  if (inicializadores[pagina]) inicializadores[pagina]();
});
