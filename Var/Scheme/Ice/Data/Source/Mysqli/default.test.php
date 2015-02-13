<?php
return [
    'time' => '2015-02-13 10:35:22',
    'revision' => '02131035',
    'tables' => [
        'acl_classes' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Classes',
            'revision' => '02131035',
        ],
        'acl_entries' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Entries',
            'revision' => '02131035',
        ],
        'acl_object_identities' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Object_Identities',
            'revision' => '02131035',
        ],
        'acl_object_identity_ancestors' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Object_Identity_Ancestors',
            'revision' => '02131035',
        ],
        'acl_security_identities' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Security_Identities',
            'revision' => '02131035',
        ],
        'classification__category' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Classification_Category',
            'revision' => '02131035',
        ],
        'classification__collection' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Classification_Collection',
            'revision' => '02131035',
        ],
        'classification__context' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Classification_Context',
            'revision' => '02131035',
        ],
        'classification__tag' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Classification_Tag',
            'revision' => '02131035',
        ],
        'ebs_article' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Article',
            'revision' => '02131035',
        ],
        'ebs_author' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Author',
            'revision' => '02131035',
        ],
        'ebs_book' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => 'Книги',
            'modelClass' => 'Ebs\\Model\\Book',
            'revision' => '02131035',
        ],
        'ebs_book_author_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Book_Author_Link',
            'revision' => '02131035',
        ],
        'ebs_book_category_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Book_Category_Link',
            'revision' => '02131035',
        ],
        'ebs_book_packet_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Book_Packet_Link',
            'revision' => '02131035',
        ],
        'ebs_bookmark' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Bookmark',
            'revision' => '02131035',
        ],
        'ebs_category' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => 'Категории - разделы наук',
            'modelClass' => 'Ebs\\Model\\Category',
            'revision' => '02131035',
        ],
        'ebs_department' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Department',
            'revision' => '02131035',
        ],
        'ebs_faculty' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Faculty',
            'revision' => '02131035',
        ],
        'ebs_favorite' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Favorite',
            'revision' => '02131035',
        ],
        'ebs_journal' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Journal',
            'revision' => '02131035',
        ],
        'ebs_journal_category_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Journal_Category_Link',
            'revision' => '02131035',
        ],
        'ebs_journal_edition' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Journal_Edition',
            'revision' => '02131035',
        ],
        'ebs_library' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Library',
            'revision' => '02131035',
        ],
        'ebs_library_user_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Library_User_Link',
            'revision' => '02131035',
        ],
        'ebs_packet' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Packet',
            'revision' => '02131035',
        ],
        'ebs_packet_dinamic' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Packet_Dinamic',
            'revision' => '02131035',
        ],
        'ebs_publisher' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Publisher',
            'revision' => '02131035',
        ],
        'ebs_publisher_user_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Publisher_User_Link',
            'revision' => '02131035',
        ],
        'ebs_summary' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Summary',
            'revision' => '02131035',
        ],
        'ebs_university' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\University',
            'revision' => '02131035',
        ],
        'ebs_university_book_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\University_Book_Link',
            'revision' => '02131035',
        ],
        'ebs_university_packet_dinamic_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\University_Packet_Dinamic_Link',
            'revision' => '02131035',
        ],
        'ebs_university_packet_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\University_Packet_Link',
            'revision' => '02131035',
        ],
        'ebs_upload_packet' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Upload_Packet',
            'revision' => '02131035',
        ],
        'ebs_upload_task' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Upload_Task',
            'revision' => '02131035',
        ],
        'fos_user_group' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Group',
            'revision' => '02131035',
        ],
        'fos_user_user' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\User',
            'revision' => '02131035',
        ],
        'fos_user_user_group' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\User_Group',
            'revision' => '02131035',
        ],
        'media__gallery' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Media_Gallery',
            'revision' => '02131035',
        ],
        'media__gallery_media' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Media_Gallery_Media',
            'revision' => '02131035',
        ],
        'media__media' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Media_Media',
            'revision' => '02131035',
        ],
        'migration_versions' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Migration_Versions',
            'revision' => '02131035',
        ],
        'pdfviewer__book' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_unicode_ci',
            'comment' => '',
            'modelClass' => 'Ebs\\Model\\Pdfviewer_Book',
            'revision' => '02131035',
        ],
    ],
];