<div class="form-group">
    <label for="comuna_selector">Seleccione su comuna:</label>
    <select id="comuna_selector" name="comuna">
        <option value="">Seleccione una comuna</option>
        {foreach from=$comunas item=comuna}
            <option value="{$comuna.id_comuna}">{$comuna.nombre}</option>
        {/foreach}
    </select>
</div>

<script>
    document.getElementById('comuna_selector').addEventListener('change', function() {
        var comuna = this.value;
        // Aquí puedes implementar la lógica para cambiar el costo de envío dependiendo de la comuna seleccionada.
    });
</script>
        