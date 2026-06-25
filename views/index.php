<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Ofertas - Suplos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <div id="app" class="container my-5">
        <header class="mb-4 text-center text-md-start">
            <h1 class="h3 text-primaryfw-bold">📦 Gestión de Ofertas Comerciales</h1>
            <p class="text-muted">Prueba Técnica Desarrollador FullStack PHP Junior — Suplos</p>
        </header>

        <!-- Alertas globales -->
        <div v-if="alert.show" :class="['alert', 'alert-dismissible', 'fade', 'show', alert.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
            {{ alert.message }}
            <button type="button" class="btn-close" @click="alert.show = false"></button>
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a :class="['nav-link', vistaActual === 'listado' ? 'active fw-bold' : '']" href="#" @click.prevent="vistaActual = 'listado'; cargarOfertas();">📋 Listado de Ofertas</a>
            </li>
            <li class="nav-item">
                <a :class="['nav-link', vistaActual === 'crear' ? 'active fw-bold' : '']" href="#" @click.prevent="vistaActual = 'crear'; resetFormulario();">➕ Nueva Oferta</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" :class="{ active: vistaActual === 'detalle' }">👁️ Detalle Oferta</a>
            </li>
        </ul>

        <div v-if="vistaActual === 'listado'" class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-middle py-3">
                <h5 class="card-title mb-0 fw-semibold align-self-center">Ofertas Publicadas en el Sistema</h5>
                <button class="btn btn-sm btn-success fw-bold" @click="descargarExcel">
                    📊 Exportar Excel
                </button>
            </div>
            <div class="card-body p-4">
                <div class="row g-2 mb-4 bg-light p-3 rounded border">
                    <h6 class="fw-bold text-muted mb-2">Filtros de búsqueda</h6>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" placeholder="Filtrar por consecutivo..." v-model="filtros.consecutivo" @input="paginacion.paginaActual = 1; cargarOfertas();">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" placeholder="Filtrar por objeto..." v-model="filtros.objeto" @input="paginacion.paginaActual = 1; cargarOfertas();">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Filtrar por descripción..." v-model="filtros.descripcion" @input="paginacion.paginaActual = 1; cargarOfertas();">
                    </div>
                </div>

                <!-- Tabla Adaptada -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Consecutivo</th>
                                <th>Objeto</th>
                                <th>Presupuesto</th>
                                <th>Cronograma Cierre</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" colspan="3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listaOfertas.length === 0">
                                <td colspan="6" class="text-center text-muted py-5">No se encontraron ofertas con los filtros aplicados.</td>
                            </tr>
                            <tr v-for="oferta in listaOfertas" :key="oferta.id">
                                <td class="font-monospace fw-bold text-secondary">{{ oferta.consecutivo }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ oferta.objeto }}</div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 400px;">{{ oferta.descripcion }}</small>
                                </td>
                                <td class="fw-medium text-nowrap">{{ oferta.moneda }} {{ Number(oferta.presupuesto).toLocaleString('es-CO') }}</td>
                                <td>
                                    <small class="d-block text-danger fw-medium">{{ oferta.fecha_cierre }}</small>
                                    <small class="text-muted">{{ oferta.hora_cierre }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill" :class="oferta.estado_calculado === 'Cerrada' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-success-subtle text-success-emphasis'">
                                        {{ oferta.estado_calculado }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary fw-medium" @click="abrirModalAdjuntos(oferta)">📎 Adjuntar</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning fw-medium" @click="editarOferta(oferta)">✏️ Editar</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info fw-medium" @click="verDetalle(oferta)">👁️ Ver</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button
                        class="btn btn-outline-secondary me-2"
                        @click="cambiarPagina(paginacion.paginaActual - 1)"
                        :disabled="paginacion.paginaActual <= 1">
                        Anterior
                    </button>
                    <span class="align-self-center">
                        Página {{ paginacion.paginaActual }} de {{ paginacion.totalPaginas }}
                    </span>
                    <button
                        class="btn btn-outline-secondary ms-2"
                        @click="cambiarPagina(paginacion.paginaActual + 1)"
                        :disabled="paginacion.paginaActual >= paginacion.totalPaginas">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>

        <div v-if="vistaActual === 'crear'" class="card shadow-sm border-0 col-lg-12 mx-auto">
            <!--Formulario de Registro-->
            
            <div class="mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-semibold">Nueva Oferta Comercial</h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="guardarOferta">
                        
                        <!-- Campos diligenciables -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Objeto de la oferta *</label>
                            <input type="text" class="form-control" v-model="formulario.objeto" maxlength="150" required placeholder="Ej: Adquisición de servidores">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Descripción completa *</label>
                            <textarea class="form-control" v-model="formulario.descripcion" rows="3" maxlength="400" required placeholder="Detalles técnicos de la oferta..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-medium">Moneda *</label>
                                <select class="form-select" v-model="formulario.moneda" required>
                                    <option value="COP">COP ($)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-medium">Presupuesto *</label>
                                <input type="number" class="form-control" v-model.number="formulario.presupuesto" step="0.01" min="0.01" required placeholder="0.00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Actividad ONU asociada *</label>
                            <input type="text" class="form-control mb-2" placeholder="Escribe para buscar (Ej: Portátiles, Software)..." v-model="busquedaActividad">
                            <select class="form-select" v-model="formulario.actividad_id" required>
                                <option value="" disabled selected>Seleccione una actividad de la lista...</option>
                                <option v-for="act in actividadesFiltradas" :key="act.id" :value="act.id">
                                    {{ act.codigo_producto }} - {{ act.producto }} ({{ act.clase }})
                                </option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Fecha Inicio *</label>
                                <input type="date" class="form-control" v-model="formulario.fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Hora Inicio *</label>
                                <input type="time" class="form-control" v-model="formulario.hora_inicio" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Fecha Cierre *</label>
                                <input type="date" class="form-control" v-model="formulario.fecha_cierre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Hora Cierre *</label>
                                <input type="time" class="form-control" v-model="formulario.hora_cierre" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mt-2" :disabled="cargando">
                            <span v-if="cargando" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Publicar Oferta
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!--Modal Cargue-->
        <div v-if="modal.show">
            <div class="modal-backdrop fade show" style="background: rgba(0,0,0,0.6);" @click="modal.show = false"></div>
            <div class="modal d-block" style="overflow-y: auto;" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        
                        <div class="modal-header bg-dark text-white py-3">
                            <h5 class="modal-title fw-semibold">📎 Documentos de la Oferta: {{ modal.ofertaSeleccionada.consecutivo }}</h5>
                            <button type="button" class="btn-close btn-close-white" @click="modal.show = false"></button>
                        </div>
                        
                        <div class="modal-body p-4">
                            
                            <!-- Formulario  -->
                            <form @submit.prevent="subirDocumento" class="bg-light p-3 rounded mb-4 border" enctype="multipart/form-data">
                                <h6 class="fw-bold mb-3 text-secondary">Subir un nuevo documento anexo</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-medium">Título del documento *</label>
                                        <input type="text" class="form-control form-control-sm" v-model.trim="modal.formulario.titulo" required placeholder="Ej: Propuesta Técnica">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-medium">Archivo (PDF o ZIP) *</label>
                                        <input type="file" class="form-control form-control-sm" id="archivoAdjunto" ref="archivoInput" accept=".pdf,.zip" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-medium">Descripción del anexo *</label>
                                    <input type="text" class="form-control form-control-sm" v-model.trim="modal.formulario.descripcion" required placeholder="Ej: Costos detallados de ingeniería.">
                                </div>
                                <button type="submit" class="btn btn-sm btn-success px-4 fw-semibold" :disabled="modal.cargando">
                                    <span v-if="modal.cargando" class="spinner-border spinner-border-sm me-1"></span>
                                    Subir Archivo
                                </button>
                            </form>

                            <h6 class="fw-bold mb-3 text-dark">Documentos cargados actualmente</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light text-secondary small">
                                        <tr>
                                            <th>Título</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="modal.documentos.length === 0">
                                            <td colspan="3" class="text-center text-muted py-4 small">No hay anexos subidos para esta oferta comercial.</td>
                                        </tr>
                                        <tr v-for="doc in modal.documentos" :key="doc.id" class="small">
                                            <td class="fw-semibold text-primary">{{ doc.titulo }}</td>
                                            <td class="text-muted">{{ doc.descripcion }}</td>
                                            <td class="text-center">
                                                <a :href="'http://localhost:8000/uploads/' + doc.archivo" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2 font-monospace">Descargar</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Detalle-->
        <div v-if="vistaActual === 'detalle'">
            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        {{ detalleOferta.consecutivo }}
                    </h4>

                    <button
                        class="btn btn-outline-secondary btn-sm"
                        @click="vistaActual='listado'">

                        ← Volver

                    </button>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Objeto</label>
                            <div>{{ detalleOferta.objeto }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Estado</label>

                            <div>
                                <span
                                    class="badge"
                                    :class="detalleOferta.estado_calculado === 'Cerrada'
                                        ? 'bg-danger'
                                        : 'bg-success'">

                                    {{ detalleOferta.estado_calculado }}

                                </span>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Descripción</label>
                            <div>{{ detalleOferta.descripcion }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Moneda</label>
                            <div>{{ detalleOferta.moneda }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Presupuesto</label>
                            <div>{{ detalleOferta.presupuesto }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Actividad</label>
                            <div>{{ detalleOferta.actividad.producto }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Fecha Inicio</label>
                            <div>
                                {{ detalleOferta.fecha_inicio }}
                                {{ detalleOferta.hora_inicio }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Fecha Cierre</label>
                            <div>
                                {{ detalleOferta.fecha_cierre }}
                                {{ detalleOferta.hora_cierre }}
                            </div>
                        </div>

                    </div>

                </div>
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-3">Documentos cargados actualmente</h6>
                    
                    <div class="table-responsive border rounded bg-white">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th class="text-center" style="width: 120px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!detalleDocumentos || detalleDocumentos.length === 0">
                                    <td colspan="3" class="text-center text-muted py-4 small">No hay anexos subidos para esta oferta comercial.</td>
                                </tr>
                                
                                <tr v-for="doc in detalleDocumentos" :key="doc.id" class="small">
                                    <td class="fw-semibold text-primary">{{ doc.titulo }}</td>
                                    <td class="text-muted">{{ doc.descripcion }}</td>
                                    <td class="text-center">
                                        <a :href="'http://localhost:8000/uploads/' + doc.archivo" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2 font-monospace">Descargar</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.18.1/dist/axios.min.js"></script>

    <script>
        const API_BASE = 'http://localhost:8000/index.php/api';

        new Vue({
            el: '#app',
            data: {
                vistaActual: 'listado',
                cargando: false,
                busquedaActividad: '',
                listaActividades: [],
                listaOfertas: [],
                detalleOferta: null,
                detalleDocumentos: [],
                filtros: {
                    consecutivo: '',
                    objeto: '',
                    descripcion: ''
                },
                paginacion: {
                    paginaActual: 1,
                    totalPaginas: 1,
                    porPagina: 10
                },
                formulario: {
                    id: '',
                    objeto: '',
                    descripcion: '',
                    moneda: 'COP',
                    presupuesto: '',
                    actividad_id: '',
                    fecha_inicio: '',
                    hora_inicio: '',
                    fecha_cierre: '',
                    hora_cierre: ''
                },
                alert: {
                    show: false,
                    message: '',
                    type: 'success'
                },
                modal: {
                    show: false,
                    cargando: false,
                    ofertaSeleccionada: {},
                    documentos: [],
                    formulario: {
                        titulo: '',
                        descripcion: ''
                    }
                }
            },
            mounted() {
                this.cargarActividades();
                this.cargarOfertas();
            },
            computed: {
                actividadesFiltradas() {
                    if (!this.listaActividades || !Array.isArray(this.listaActividades)) {
                        return [];
                    }

                    if (!this.busquedaActividad || this.busquedaActividad.trim() === '') {
                        return this.listaActividades.slice(0, 50);
                    }

                    const termino = this.busquedaActividad.toLowerCase().trim();

                    const filtradas = this.listaActividades.filter(act => {
                        return (act.producto && act.producto.toLowerCase().includes(termino)) || 
                            (act.codigo_producto && act.codigo_producto.toString().includes(termino)) ||
                            (act.clase && act.clase.toLowerCase().includes(termino));
                    });

                    return filtradas.slice(0, 50);
                }
            },
            methods: {
                
                //llenar el selector desplegable
                cargarActividades() {
                    axios.get(`${API_BASE}/actividades`)
                        .then(res => { 
                            this.listaActividades = res.data; 
                        })
                        .catch(err => { 
                            this.mostrarAlerta("Error al cargar actividades maestras", "error"); 
                        });
                },

                //ofertas guardadas para mostrarlas
                cargarOfertas() {
                    const params = {
                        page: this.paginacion.paginaActual,
                        consecutivo: this.filtros.consecutivo,
                        objeto: this.filtros.objeto,
                        descripcion: this.filtros.descripcion
                    };
                    
                    axios.get(`${API_BASE}/ofertas`, { params })
                    .then(res => {
                        this.listaOfertas = res.data.data || [];
                        this.paginacion.totalPaginas = res.data.last_page || 1;
                    })
                    
                },

                cambiarPagina(pagina) {
                    if (pagina < 1 || pagina > this.paginacion.totalPaginas) return;
                    this.paginacion.paginaActual = pagina;
                    this.cargarOfertas();
                },

                descargarExcel() {
                    const queryParams = new URLSearchParams(this.filtros).toString();
                    window.open(`${API_BASE}/ofertas/exportar?${queryParams}`, '_blank');
                },

                guardarOferta() {
                const inicio = new Date(`${this.formulario.fecha_inicio}T${this.formulario.hora_inicio}`);
                const cierre = new Date(`${this.formulario.fecha_cierre}T${this.formulario.hora_cierre}`);

                if (inicio >= cierre) {
                    this.mostrarAlerta("La fecha y hora de cierre deben ser posteriores a la fecha y hora de inicio.", "error");
                    return;
                }
                    this.cargando = true;

                    const esEdicion = this.formulario.id && this.formulario.id !== '';

                    const request = esEdicion
                        ? axios.put(
                            `${API_BASE}/ofertas/${this.formulario.id}`,
                            this.formulario
                        )
                        : axios.post(
                            `${API_BASE}/ofertas`,
                            this.formulario
                        );

                    request
                        .then(res => {
                            this.mostrarAlerta(res.data.message, "success");
                            this.resetFormulario();
                            this.cargarOfertas();
                            this.vistaActual = 'listado';
                        })
                        .catch(err => {
                            const msg =
                                err.response && err.response.data.error
                                    ? err.response.data.error
                                    : "Error de comunicación";

                            this.mostrarAlerta(msg, "error");
                        })
                        .finally(() => {
                            this.cargando = false;
                        });
                },

                editarOferta(oferta) {
                    this.formulario = {
                        id: oferta.id,
                        objeto: oferta.objeto,
                        descripcion: oferta.descripcion,
                        moneda: oferta.moneda,
                        presupuesto: oferta.presupuesto,
                        actividad_id: oferta.actividad_id,
                        fecha_inicio: oferta.fecha_inicio,
                        hora_inicio: oferta.hora_inicio,
                        fecha_cierre: oferta.fecha_cierre,
                        hora_cierre: oferta.hora_cierre
                    };

                    this.vistaActual = 'crear';
                },

                //mensajes flotantes en la pantalla
                mostrarAlerta(message, type) {
                    this.alert.message = message;
                    this.alert.type = type;
                    this.alert.show = true;
                    
                    setTimeout(() => { this.alert.show = false; }, 5000);
                },

                //devuelve los campos a vacío
                resetFormulario() {
                    this.formulario = {
                        objeto: '',
                        descripcion: '',
                        moneda: 'COP',
                        presupuesto: '',
                        actividad_id: '',
                        fecha_inicio: '',
                        hora_inicio: '',
                        fecha_cierre: '',
                        hora_cierre: ''
                    };
                },

                //modal de carga
                abrirModalAdjuntos(oferta) {
                    this.modal.ofertaSeleccionada = oferta;
                    this.modal.formulario.titulo = '';
                    this.modal.formulario.descripcion = '';
                    this.modal.documentos = [];
                    this.modal.show = true;
                    this.cargarDocumentos(oferta.id);
                },   

                //consulta documentos
                cargarDocumentos(ofertaId) {
                    axios.get(`${API_BASE}/ofertas/documentos?licitacion_id=${ofertaId}`)
                        .then(res => {
                            this.modal.documentos = res.data;
                        })
                        .catch(err => {
                            this.mostrarAlerta("Error al cargar los documentos anexos", "error");
                        });
                },

                //envio de archivo al sv
                subirDocumento() {
                    const fileInput = this.$refs.archivoInput;
                    if (!fileInput || fileInput.files.length === 0) {
                        this.mostrarAlerta("Por favor seleccione un archivo PDF o ZIP", "error");
                        return;
                    }

                    this.modal.cargando = true;

                    let formData = new FormData();
                    formData.append('licitacion_id', this.modal.ofertaSeleccionada.id);
                    formData.append('titulo', this.modal.formulario.titulo);
                    formData.append('descripcion', this.modal.formulario.descripcion);
                    formData.append('archivo', fileInput.files[0]);

                    axios.post(`${API_BASE}/ofertas/documentos`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    })
                    .then(res => {
                        this.mostrarAlerta(res.data.message, "success");
                        this.modal.formulario.titulo = '';
                        this.modal.formulario.descripcion = '';
                        if (fileInput) fileInput.value = '';
                        this.cargarDocumentos(this.modal.ofertaSeleccionada.id);
                    })
                    .catch(err => {
                        const msg = err.response && err.response.data.error ? err.response.data.error : "Error al subir archivo";
                        this.mostrarAlerta(msg, "error");
                    })
                    .finally(() => {
                        this.modal.cargando = false;
                    });
                },

                verDetalle(oferta) {

                    this.detalleOferta = oferta;
                    this.detalleDocumentos = [];

                    axios.get(
                        `${API_BASE}/ofertas/documentos?licitacion_id=${oferta.id}`
                    )
                    .then(res => {

                        this.detalleDocumentos = res.data;
                        this.vistaActual = 'detalle';

                    })
                    .catch(err => {

                        this.mostrarAlerta(
                            "Error al cargar documentos del detalle",
                            "error"
                        );

                    });

                },
            }
        });
    </script>