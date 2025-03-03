<!-- Selector de Integrante -->
<div class="mb-3">
    <select class="form-select" id="selectIntegrante">
        <option value="">Seleccione un integrante...</option>
        @if(isset($integrantes))
            @foreach($integrantes as $integrante)
                <option value="{{ $integrante->idintegrante }}">
                    {{ $integrante->nombrecompleto }} - {{ $integrante->documento }}
                </option>
            @endforeach
        @endif
    </select>
</div>

<!-- Contenedor de logros del integrante -->
<div id="logrosContainer" style="display: none;">
    <div class="accordion" id="dimensionesAccordion">
        <!-- Las dimensiones y logros se cargarán dinámicamente -->
    </div>
</div>

<!-- Template para dimensiones -->
<template id="dimension-template">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dimension-{id}">
                {dimension}
            </button>
        </h2>
        <div id="dimension-{id}" class="accordion-collapse collapse" data-bs-parent="#dimensionesAccordion">
            <div class="accordion-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead style="background-color: #0D6EFD; color: white;">
                            <tr>
                                <th>Logro</th>
                                <th>Estado Inicial</th>
                                <th>Estado Final</th>
                                <th>Fecha Actualización</th>
                            </tr>
                        </thead>
                        <tbody id="logros-dimension-{id}">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Template para logros -->
<template id="logro-template">
    <tr>
        <td>{logro}</td>
        <td><span class="badge {claseDI}">{estadoDI}</span></td>
        <td><span class="badge {clasePF}">{estadoPF}</span></td>
        <td>{fecha}</td>
    </tr>
</template>

<style>
.badge-rojo { background-color: #dc3545; color: white; }
.badge-verde { background-color: #198754; color: white; }
.badge-gris { background-color: #6c757d; color: white; }
.badge-azul { background-color: #0d6efd; color: white; }
.badge-cafe { background-color: #795548; color: white; }
</style>
