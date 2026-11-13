<?php

return [
    'title' => 'Monitorización de Elasticsearch', 'subtitle' => 'Resumen del clúster', 'refresh' => 'Actualizar',
    'status' => 'Estado del clúster', 'nodes' => 'Nodos', 'documents' => 'Documentos', 'active_shards' => 'Fragmentos activos',
    'search_rate' => 'Búsquedas / s', 'index_rate' => 'Indexaciones / s', 'unassigned_shards' => 'Fragmentos sin asignar',
    'search_rejections' => 'Rechazos de búsqueda', 'heap' => 'Heap de JVM', 'filesystem' => 'Sistema de archivos',
    'no_metrics' => 'Aún no hay métricas. Ejecuta php artisan elasticsearch:collect.', 'last_collected' => 'Última recopilación',
    'load_error' => 'No se pudieron cargar los datos', 'heap_used' => 'Uso del heap %', 'used' => 'Usado', 'available' => 'Disponible',
];
