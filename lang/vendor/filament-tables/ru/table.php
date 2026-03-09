<?php

return [
    'columns' => [
        'text' => [
            'actions' => [
                'collapse_list' => 'Показать на :count меньше',
                'expand_list' => 'Показать еще :count',
            ],
            'more_list_items' => 'и еще :count',
        ],
    ],
    'data_loading' => [
        'label' => 'Загрузка...',
    ],
    'fields' => [
        'search' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
            'indicator' => 'Поиск',
        ],
    ],
    'pagination' => [
        'label' => 'Навигация по страницам',
        'overview' => 'Показано с :first по :last из :total результатов',
        'fields' => [
            'records_per_page' => [
                'label' => 'на странице',
                'options' => [
                    'all' => 'Все',
                ],
            ],
        ],
        'actions' => [
            'first' => [
                'label' => 'Первая',
            ],
            'go_to_page' => [
                'label' => 'Перейти на страницу :page',
            ],
            'last' => [
                'label' => 'Последняя',
            ],
            'next' => [
                'label' => 'Следующая',
            ],
            'previous' => [
                'label' => 'Предыдущая',
            ],
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'Создать',
        ],
        'delete' => [
            'label' => 'Удалить',
        ],
        'edit' => [
            'label' => 'Редактировать',
        ],
        'filter' => [
            'label' => 'Фильтр',
        ],
        'open' => [
            'label' => 'Открыть',
        ],
        'view' => [
            'label' => 'Просмотр',
        ],
    ],
    'empty' => [
        'heading' => 'Записи не найдены',
        'description' => 'Создайте запись для начала работы.',
    ],
    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'Применить',
            ],
            'remove' => [
                'label' => 'Убрать фильтр',
            ],
            'remove_all' => [
                'label' => 'Убрать все фильтры',
                'tooltip' => 'Убрать все фильтры',
            ],
            'reset' => [
                'label' => 'Сбросить',
            ],
        ],
        'heading' => 'Фильтры',
        'indicator' => 'Активные фильтры',
        'multi_select' => [
            'placeholder' => 'Все',
        ],
        'select' => [
            'placeholder' => 'Все',
        ],
        'trashed' => [
            'label' => 'Удаленные записи',
            'only_trashed' => 'Только удаленные',
            'with_trashed' => 'Вместе с удаленными',
            'without_trashed' => 'Без удаленных',
        ],
    ],
    'reorder_indicator' => 'Перетащите записи в нужном порядке.',
    'selection_indicator' => [
        'selected_count' => 'Выбрано :count запись|Выбрано :count записи|Выбрано :count записей',
        'actions' => [
            'select_all' => [
                'label' => 'Выбрать все :count',
            ],
            'deselect_all' => [
                'label' => 'Снять выбор со всех',
            ],
        ],
    ],
    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Сортировать по',
            ],
            'direction' => [
                'label' => 'Направление сортировки',
                'options' => [
                    'asc' => 'По возрастанию',
                    'desc' => 'По убыванию',
                ],
            ],
        ],
    ],
];
