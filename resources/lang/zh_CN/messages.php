<?php

return [
    'title' => 'Elasticsearch 监控', 'subtitle' => '集群概览', 'refresh' => '刷新',
    'status' => '集群状态', 'nodes' => '节点', 'documents' => '文档', 'active_shards' => '活动分片',
    'search_rate' => '每秒搜索', 'index_rate' => '每秒索引', 'unassigned_shards' => '未分配分片',
    'search_rejections' => '搜索拒绝', 'heap' => 'JVM 堆', 'filesystem' => '文件系统',
    'no_metrics' => '尚无指标。请运行 php artisan elasticsearch:collect。', 'last_collected' => '上次采集',
    'load_error' => '无法加载监控数据', 'heap_used' => '堆使用率 %', 'used' => '已用', 'available' => '可用',
    'search_queue' => '搜索队列',
    'write_queue' => '写入队列',
    'write_rejections' => '写入拒绝',
    'index_count' => '索引数量',
    'store_size' => '索引存储',
    'rates' => '每秒操作数',
];
