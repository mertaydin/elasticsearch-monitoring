<?php

return [
    'title' => 'مراقبة Elasticsearch', 'subtitle' => 'نظرة عامة على المجموعة', 'refresh' => 'تحديث',
    'status' => 'حالة المجموعة', 'nodes' => 'العُقد', 'documents' => 'المستندات', 'active_shards' => 'الأجزاء النشطة',
    'search_rate' => 'عمليات البحث / ثانية', 'index_rate' => 'الفهرسة / ثانية', 'unassigned_shards' => 'الأجزاء غير المعيّنة',
    'search_rejections' => 'عمليات البحث المرفوضة', 'heap' => 'ذاكرة JVM', 'filesystem' => 'نظام الملفات',
    'no_metrics' => 'لا توجد مقاييس بعد. شغّل php artisan elasticsearch:collect.', 'last_collected' => 'آخر جمع',
    'load_error' => 'تعذّر تحميل بيانات المراقبة', 'heap_used' => 'استخدام الذاكرة %', 'used' => 'مستخدم', 'available' => 'متاح',
];
