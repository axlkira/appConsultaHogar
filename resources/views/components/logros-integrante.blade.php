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
        <td>
            <a href="#" class="logro-link" 
               data-idlogro="{idlogro}" 
               data-iddimension="{iddimension}" 
               data-folio="{folio}" 
               data-idintegrante="{idintegrante}">
                {logro}
            </a>
        </td>
        <td><span class="badge bg-{claseDI}">{estadoDI}</span></td>
        <td><span class="badge bg-{clasePF}">{estadoPF}</span></td>
    </tr>
</template>

<style>
.badge-rojo { 
    background-color: #DF0101; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-verde { 
    background-color: #30E24E; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-gris { 
    background-color: #D1DFD3; 
    color: black; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-azul { 
    background-color: #428BCA; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-cafe { 
    background-color: #8B4513; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-blanco { 
    background-color: #FFFFFF; 
    color: black; 
    border: 1px solid #ccc;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
