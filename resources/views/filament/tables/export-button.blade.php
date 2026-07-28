<x-filament::icon-button
    tag="button"
    icon="heroicon-o-arrow-down-tray"
    label="Exportar XLSX"
    color="gray"
    size="md"
    x-data="{
        doExport() {
            const resource = '{{ request()->segment(2) }}'
            const searchInput = this.$el.closest('.fi-ta-header-toolbar')?.querySelector('.fi-ta-search-field input')
            const search = searchInput?.value ?? ''

            const params = new URLSearchParams({ resource, search })

            window.open('/admin/export?' + params.toString(), '_blank')
        }
    }"
    x-on:click="doExport()"
/>
