<?php

/**
 * Russian Language File for the Admin Module (admin.ru.lang.php)
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Common words
 */
$L['Extension'] = 'Расширение';
$L['Extensions'] = 'Расширения';
$L['Structure'] = 'Структура';

/**
 * Home Section
 */
$L['home_installable_error'] = 'Пожалуйста, удалите install.php до следующего обновления или по крайней мере защитите config.php от записи';

$L['home_newusers'] = 'Новые пользователи';
$L['home_newpages'] = 'Новые страницы';
$L['home_newtopics'] = 'Новые темы';
$L['home_newposts'] = 'Новые сообщения на форуме';
$L['home_newpms'] = 'Новые личные сообщения';

$L['home_db_rows'] = 'БД SQL, строк';
$L['home_db_indexsize'] = 'БД SQL, размер индекса (KB)';
$L['home_db_datassize'] = 'БД SQL, размер данных (KB)';
$L['home_db_totalsize'] = 'БД SQL, общий размер (KB)';

$L['home_ql_b1_title'] = 'Настройки сайта';
$L['home_ql_b1_1'] = 'Основные настройки системы';
$L['home_ql_b1_2'] = 'Заголовки (тэг &lt;title&gt;)';
$L['home_ql_b1_3'] = 'Скины и кодировка';
$L['home_ql_b1_4'] = 'Слоты для меню в tpl-файлах';
$L['home_ql_b1_5'] = 'Язык сайта ';
$L['home_ql_b1_6'] = 'Время и дата';

$L['home_ql_b2_1'] = 'Структура страниц и категорий';
$L['home_ql_b2_2'] = 'Экстраполя для страниц';
$L['home_ql_b2_3'] = 'Экстраполя для категорий';
$L['home_ql_b2_4'] = 'Настройки парсинга';

$L['home_ql_b3_1'] = 'Настройка пользователей';
$L['home_ql_b3_2'] = 'Экстраполя для профиля';
$L['home_ql_b3_4'] = 'Права групп';

$L['home_update_notice'] = 'Доступно обновление';
$L['home_update_revision'] = 'Текущая версия: <span style="color:#C00;font-weight:bold;">%1$s</span><br />Новая версия: <span style="color:#4E9A06;font-weight:bold;">%2$s</span>'; // %1/%2 Current Version/Revision %3/%4 Updated Version/Revision

/**
 * Config Section
 */
$L['core_forums'] = &$L['Forums'];
$L['core_locale'] = &$L['Locale'];
$L['core_main'] = 'Настройки сайта';
$L['core_menus'] = &$L['Menus'];
$L['core_page'] = &$L['Pages'];
$L['core_parser'] = &$L['Parser'];
$L['core_performance'] = 'Производительность';
$L['core_pfs'] = &$L['PFS'];
$L['core_plug'] = &$L['Plugins'];
$L['core_pm'] = &$L['Private_Messages'];
$L['core_polls'] = &$L['Polls'];
$L['core_rss'] = &$L['RSS_Feeds'];
$L['core_security'] = &$L['Security'];
$L['core_sessions'] = 'Сессий';
$L['core_structure'] = &$L['Categories'];
$L['core_theme'] = &$L['Themes'];
$L['core_time'] = 'Время и дата';
$L['core_title'] = 'Заголовки и мета-теги';

$L['cfg_struct_defaults'] = 'Настройки по умолчанию для структуры';

/**
 * Shortcuts
 */
$L['short_admin'] = 'Админ';
$L['short_config'] = 'Конфиг';
$L['short_delete'] = 'Удалить';
$L['short_open'] = 'Открыть';
$L['short_options'] = 'Опции';
$L['short_rights'] = 'Права';
$L['short_struct'] = 'Структ';

/**
 * Config Section
 * Locale Subsection
 */
$L['cfg_forcedefaultlang'] = array('Принудительная установка языка по умолчанию для всех пользователей', ' ');
$L['cfg_defaulttimezone'] = array('Часовой пояс по умолчанию', 'Для гостей и при регистрации, от -12 до +12');

/**
 * Config Section
 * Main Subsection
 */
$L['cfg_adminemail'] = array('E-mail администратора сайта', 'Обязательно!');
$L['cfg_clustermode'] = array('Серверный кластер', 'Выберите Да, если используется кластерная система балансировки нагрузок.');
$L['cfg_confirmlinks'] = array('Подтверждать потенциально опасные действия');
$L['cfg_easypagenav'] = array('Дружественная паджинация', 'Использует номера страниц в ссылках вместо смещений БД');
$L['cfg_hostip'] = array('IP-адрес сервера', 'Необязательно');
$L['cfg_maxrowsperpage'] = array('Макс. количество элементов на страницу', 'Стандартный лимит элементов для паджинации');
$L['cfg_parser'] = array('Парсер разметки', 'По умолчанию: простой текст');

/**
 * Config Section
 * Menus Subsection
 */
$L['cfg_banner'] = array('Баннер<br />{HEADER_BANNER} в header.tpl', ' ');
$L['cfg_bottomline'] = array('Нижняя строка<br />{FOOTER_BOTTOMLINE} в footer.tpl', ' ');
$L['cfg_topline'] = array('Верхняя строка<br />{HEADER_TOPLINE} в header.tpl', ' ');

$L['cfg_menu1'] = array('Меню #1<br />{PHP.cfg.menu1} во всех файлах .tpl', ' ');
$L['cfg_menu2'] = array('Меню #2<br />{PHP.cfg.menu2} во всех файлах .tpl', ' ');
$L['cfg_menu3'] = array('Меню #3<br />{PHP.cfg.menu3} во всех файлах .tpl', ' ');
$L['cfg_menu4'] = array('Меню #4<br />{PHP.cfg.menu4} во всех файлах .tpl', ' ');
$L['cfg_menu5'] = array('Меню #5<br />{PHP.cfg.menu5} во всех файлах .tpl', ' ');
$L['cfg_menu6'] = array('Меню #6<br />{PHP.cfg.menu6} во всех файлах .tpl', ' ');
$L['cfg_menu7'] = array('Меню #7<br />{PHP.cfg.menu7} во всех файлах .tpl', ' ');
$L['cfg_menu8'] = array('Меню #8<br />{PHP.cfg.menu8} во всех файлах .tpl', ' ');
$L['cfg_menu9'] = array('Меню #9<br />{PHP.cfg.menu9} во всех файлах .tpl', ' ');

$L['cfg_freetext1'] = array('Текст #1<br />{PHP.cfg.freetext1} во всех файлах .tpl', ' ');
$L['cfg_freetext2'] = array('Текст #2<br />{PHP.cfg.freetext2} во всех файлах .tpl', ' ');
$L['cfg_freetext3'] = array('Текст #3<br />{PHP.cfg.freetext3} во всех файлах .tpl', ' ');
$L['cfg_freetext4'] = array('Текст #4<br />{PHP.cfg.freetext4} во всех файлах .tpl', ' ');
$L['cfg_freetext5'] = array('Текст #5<br />{PHP.cfg.freetext5} во всех файлах .tpl', ' ');
$L['cfg_freetext6'] = array('Текст #6<br />{PHP.cfg.freetext6} во всех файлах .tpl', ' ');
$L['cfg_freetext7'] = array('Текст #7<br />{PHP.cfg.freetext7} во всех файлах .tpl', ' ');
$L['cfg_freetext8'] = array('Текст #8<br />{PHP.cfg.freetext8} во всех файлах .tpl', ' ');
$L['cfg_freetext9'] = array('Текст #9<br />{PHP.cfg.freetext9} во всех файлах .tpl', ' ');

/**
 * Config Section
 * Performance Subsection
 */
$L['cfg_gzip'] = array('Gzip', 'Gzip-сжатие для исходящего HTML-кода. Не включайте эту опцию, если ваш сервер уже применяет Gzip к страницам сайта. Проверьте, включено ли Gzip-сжатие на вашем сайте, с помощью этого инструмента: <a href="http://www.whatsmyip.org/http-compression-test/">HTTP Compression Test</a>');
$L['cfg_headrc_consolidate'] = array('Объединять ресурсы header/footer (JS/CSS)');
$L['cfg_headrc_minify'] = array('Минифицировать объединённые JS/CSS');
$L['cfg_jquery_cdn'] = array('Использовать jQuery из CDN по этой ссылке', 'Пример: https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js');
$L['cfg_jquery'] = array('Включить jQuery', ' ');
$L['cfg_turnajax'] = array('Включить Ajax', 'Работает только если jQuery включен');

/**
 * Config Section
 * Security Subsection
 */
$L['cfg_captchamain'] = array('Captcha по умолчанию');
$L['cfg_captcharandom'] = array('Случайный выбор captcha');
$L['cfg_hashfunc'] = array('Функция хеширования по умолчанию', 'Используется для хеширования паролей');
$L['cfg_referercheck'] = array('Проверка referer для форм', 'Предотвращает междоменный постинг');
$L['cfg_shieldenabled'] = array('Включить защиту', 'Защита против спама и хаммеринга');
$L['cfg_shieldtadjust'] = array('Настройка таймеров защиты (в %)', 'Чем выше, тем сильнее защита против спама');
$L['cfg_shieldzhammer'] = array('Анти-хаммер после * хитов', 'Чем меньше, тем короче срок автоблокировки пользователя');
$L['cfg_devmode'] = array('Режим отладки', 'Только для отладки под localhost');
$L['cfg_maintenance'] = array('Режим обслуживания', 'Доступа к сайту разрешен только администраторам');
$L['cfg_maintenancereason'] = array('Причина режима обслуживания', 'Коротко опишите почему сайт находится в режиме обслуживания');

/**
 * Config Section
 * Sessions Subsection
 */
$L['cfg_cookiedomain'] = array('Домен для cookies', 'По умолчанию пусто');
$L['cfg_cookielifetime'] = array('Срок действия cookies', 'В секундах');
$L['cfg_cookiepath'] = array('Путь для cookies', 'По умолчанию пусто');
$L['cfg_forcerememberme'] = array('Зафиксировать &quot;запомнить меня&quot;', 'Используйте на мультидоменных сайтах или при случайных выходах из системы');
$L['cfg_timedout'] = array('Задержка ожидания в секундах', 'По истечении данного срока пользователь считается покинувшим сайт');
$L['cfg_redirbkonlogin'] = array('Возврат после авторизации', 'Вернуться на страницу, посещённую перед авторизацией');
$L['cfg_redirbkonlogout'] = array('Возврат после выхода', 'Вернуться на страницу, посещённую перед выходом');

/**
 * Config Section
 * Themes Subsection
 */
$L['cfg_charset'] = array('Набор символов (кодовая страница)', ' ');
$L['cfg_disablesysinfos'] = array('Отключить время создания страницы', '(в footer.tpl)');
$L['cfg_doctypeid'] = array('Тип документа', '&lt;!DOCTYPE&gt; в HTML-разметке');
$L['cfg_forcedefaulttheme'] = array('Принудительная установка темы по умолчанию для всех пользователей', ' ');
$L['cfg_homebreadcrumb'] = array('Ссылка на главную страницу в &laquo;навигационной цепочке&raquo;', 'Установить ссылку на главную страницу в начале &laquo;навигационной цепочки&raquo;');
$L['cfg_keepcrbottom'] = array('Оставить копирайт в тэге {FOOTER_BOTTOMLINE}', '(в footer.tpl)');
$L['cfg_metakeywords'] = array('Ключевые слова', '(через запятую)');
$L['cfg_msg_separate'] = array('Показывать сообщения отдельно для каждого источника', '');
$L['cfg_separator'] = array('Разделитель', '(используется в навигационной цепочке и т .д.)');
$L['cfg_showsqlstats'] = array('Показывать статистику SQL-запросов', '(в footer.tpl)');

/**
 * Config Section
 * Title Subsection
 */
$L['cfg_maintitle'] = array('Название сайта', 'Обязательно');
$L['cfg_subtitle'] = array('Описание сайта', 'Необязательно');
$L['cfg_title_header'] = array('Основной заголовок', 'Опции: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}');
$L['cfg_title_header_index'] = array('Заголовок главной страницы', 'Опции: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}');
$L['cfg_title_users_details'] = array('Пользователи - просмотр профиля', 'Опции: {USER}, {NAME}');
$L['cfg_subject_mail'] = array('Заголовок email', 'Опции: {SITE_TITLE}, {SITE_DESCRIPTION}, {MAIL_SUBJECT}');
$L['cfg_body_mail'] = array('Текст email', 'Опции: {SITE_TITLE}, {SITE_DESCRIPTION}, {SITE_URL}, {ADMIN_EMAIL}, {MAIL_BODY}, {MAIL_SUBJECT}');

/**
 * Config Section
 * Common strings
 */
$L['cfg_css'] = array('Подключить CSS модуля/плагина');
$L['cfg_editor'] = array('Редактор разметки', '');
$L['cfg_markup'] = array('Включить разметку', 'Включает HTML/BBcode или другой парсинг, установленный в вашей системе');

/**
 * Extension management
 */
$L['ext_already_installed'] = 'Данное расширение уже установлено: {$name}';
$L['ext_auth_installed'] = 'Значения авторизации по умолчанию установлены';
$L['ext_auth_locks_updated'] = 'Блокировки авторизации обновлены';
$L['ext_auth_uninstalled'] = 'Опции авторизации удалены';
$L['ext_bindings_installed'] = 'Установлено связок хуков: {$cnt}';
$L['ext_bindings_uninstalled'] = 'Удалено связок хуков: {$cnt}';
$L['ext_config_error'] = 'Ошибка настройки конфигурации';
$L['ext_config_installed'] = 'Конфигурация установлена';
$L['ext_config_uninstalled'] = 'Конфигурация удалена';
$L['ext_config_updated'] = 'Опции конфигурации обновлены';
$L['ext_config_struct_error'] = 'Ошибка настройки конфигурации структуры';
$L['ext_config_struct_installed'] ='Конфигурация структуры установлена';
$L['ext_config_struct_updated'] = 'Опции конфигурации структуры обновлены';
$L['ext_dependency_error'] = '{$dep_type} &quot;{$dep_name}&quot;, необходимый для {$type} &quot;{$name}&quot;, не установлен и не выбран для установки';
$L['ext_dependency_uninstall_error'] = '{$type} &quot;{$name}&quot; использует данное расширение и должен быть удален в первую очередь';
$L['ext_executed_php'] = 'Выполнена часть PHP-хэндлера: {$ret}';
$L['ext_executed_sql'] = 'Выполнена часть SQL-хэндлера: {$ret}';
$L['ext_installing'] = 'Установка {$type} &quot;{$name}&quot;';
$L['ext_invalid_format'] = 'Расширение несовместимо с Cotonti версии 0.9 и выше. Пожалуйста, свяжитесь с разработчиками.';
$L['ext_old_format'] = 'Это старый плагин для Genoa/Seditio. Он может работать некорректно или не работать вовсе.';
$L['ext_patch_applied'] = 'Установлен патч {$f}: {$msg}';
$L['ext_patch_error'] = 'Ошибка установки патча {$f}: {$msg}';
$L['ext_requires_modules'] = 'Необходимые модули';
$L['ext_requires_plugins'] = 'Необходимые плагины';
$L['ext_recommends_modules'] = 'Рекомендуемые модули';
$L['ext_recommends_plugins'] = 'Рекомендуемые плагины';
$L['ext_setup_not_found'] = 'Файл установок не найден: {$path}';
$L['ext_uninstall_confirm'] = 'Вы действительно хотите удалить это расширение? Все данные, связанные с этим расширением, будут удалены без возможности восстановления.<br/><a href="{$url}">Да, удалить вместе с данными.</a>';
$L['ext_uninstalling'] = 'Удаление {$type} &quot;{$name}&quot;';
$L['ext_up2date'] = '{$type} &quot;{$name}&quot; не требует обновления';
$L['ext_update_error'] = 'Ошибка обновления {$type} &quot;{$name}&quot;';
$L['ext_updated'] = '{$type} &quot;{$name}&quot; обновлен до версии {$ver}';
$L['ext_updating'] = 'Обновление {$type} &quot;{$name}&quot;';

/**
 * Extension categories
 */
$L['ext_cat']['administration-management'] = 'Администрирование и управление';
$L['ext_cat']['commerce'] = 'Электронная коммерция';
$L['ext_cat']['community-social'] = 'Сообщества и социальное';
$L['ext_cat']['customization-i18n'] = 'Тонкая настройка и I18n';
$L['ext_cat']['data-apis'] = 'Потоки данных и API';
$L['ext_cat']['development-maintenance'] = 'Разработка и поддержка';
$L['ext_cat']['editor-parser'] = 'Редакторы и разметка';
$L['ext_cat']['files-media'] = 'Файлы и медиа';
$L['ext_cat']['forms-feedback'] = 'Формы и обратная связь';
$L['ext_cat']['gaming-clans'] = 'Игры и кланы';
$L['ext_cat']['intranet-groupware'] = 'Корпоративный сектор';
$L['ext_cat']['misc-ext'] = 'Прочее';
$L['ext_cat']['mobile-geolocation'] = 'Мобильность и геолокация';
$L['ext_cat']['navigation-structure'] = 'Навигация и структура';
$L['ext_cat']['performance-seo'] = 'Производительность и SEO';
$L['ext_cat']['publications-events'] = 'Публикации и события';
$L['ext_cat']['security-authentication'] = 'Безопасность и аутентификация';
$L['ext_cat']['utilities-tools'] = 'Инструменты';
$L['ext_cat']['post-install'] = 'Пост-установочные скрипты';

/**
 * Structure Section
 */
$L['adm_structure_code_reserved'] = "Structure code 'all' is reserved.";
$L['adm_structure_code_required'] = 'Missing required field: Code';
$L['adm_structure_path_required'] = 'Missing required field: Path';
$L['adm_structure_title_required'] = 'Missing required field: Title';
$L['adm_cat_exists'] = 'Категория с таким кодом уже существует';
$L['adm_tpl_mode'] = 'Установка шаблона';
$L['adm_tpl_empty'] = 'По умолчанию';
$L['adm_tpl_forced'] = 'Как';
$L['adm_tpl_parent'] = 'Как родительская категория';
$L['adm_tpl_quickcat'] = 'Код категории';
$L['adm_tpl_resyncalltitle'] = 'Синхронизировать все счетчики страниц';
$L['adm_tpl_resynctitle'] = 'Синхронизировать счетчики страниц в разделе';
$L['adm_help_structure'] = 'Страницы категории &laquo;system&raquo; не отображаются в списках страниц и являются отдельными, самостоятельными страницами';

/**
 * Structure Section
 * Extrafields Subsection
 */
$L['adm_extrafields_desc'] = 'Создание / правка экстраполей';
$L['adm_extrafields_all'] = 'Все таблицы';
$L['adm_extrafields_table'] = 'Таблица';
$L['adm_extrafields_help_notused'] = 'Не используется';
$L['adm_extrafields_help_variants'] = '{значение1},{значение2},{значение3},...';
$L['adm_extrafields_help_range'] = '{мин_значение},{макс_значение}';
$L['adm_extrafields_help_data'] = '{мин_год},{макс_год},{формат_даты}. Если не указан {формат_даты}, выводится stamp';
$L['adm_extrafields_help_regex'] = 'Регулярное выражение для ввода значение';
$L['adm_extrafields_help_file'] = 'Директория для загрузки файлов';
$L['adm_extrafields_help_separator'] = 'Разделитель значений';
$L['adm_help_info'] = 'HTML-код поля установится в значение по умолчанию автоматически, если его очистить и обновить';
$L['adm_help_newtags'] = '<br /><br /><b>Новые тэги в tpl-файлах:</b>';

/**
 * Users Section
 */
$L['adm_rightspergroup'] = 'Права групп';
$L['adm_maxsizesingle'] = 'Максимальный размер одного файла в разделе &laquo;'.$L['PFS'].'&raquo; (Кб)';
$L['adm_maxsizeallpfs'] = 'Максимальный размер всех файлов в разделе &laquo;'.$L['PFS'].'&raquo; (Кб)';
$L['adm_copyrightsfrom'] = 'Установить права как в группе';
$L['adm_rights_maintenance'] = 'Разрешить авторизацию при включенном режиме обслуживания';
$L['adm_skiprights'] = 'Пропустить права для этой группы';

/**
 * Plug Section
 */
$L['adm_defauth_guests'] = 'Права гостей по умолчанию';
$L['adm_deflock_guests'] = 'Блокировать гостей по маске';
$L['adm_defauth_members'] = 'Права пользователей по умолчанию';
$L['adm_deflock_members'] = 'Блокировать пользователей по маске';

$L['adm_present'] = 'Присутствует';
$L['adm_missing'] = 'Отсутствует';
$L['adm_paused'] = 'Выполнение приостановлено';
$L['adm_running'] = 'Запущен';
$L['adm_partrunning'] = 'Запущен частично';
$L['adm_partstopped'] = 'Частично остановлен';
$L['adm_installed'] = 'Установлен';
$L['adm_notinstalled'] = 'Не установлен';

$L['adm_plugsetup'] = 'Настройки плагина';
$L['adm_override_guests'] = 'Системная блокировка: незарегистрированным и неактивированным пользователям доступ к администрированию запрещен';
$L['adm_override_banned'] = 'Системная блокировка: учетная запись заблокирована';
$L['adm_override_admins'] = 'Системная блокировка: администраторы';

$L['adm_opt_install'] = 'Установить';
$L['adm_opt_install_explain'] = 'Установка или сброс всех компонентов плагина в значения по умолчанию';
$L['adm_opt_pause'] = 'Приостановить';
$L['adm_opt_pauseall'] = 'Приостановить все';
$L['adm_opt_pauseall_explain'] = 'Остановка выполнения всех компонентов плагина';
$L['adm_opt_update'] = 'Обновить';
$L['adm_opt_update_explain'] = 'Обновление конфигурации и данных если файлы расширения на носителе уже обновлены';
$L['adm_opt_uninstall'] = 'Удалить';
$L['adm_opt_uninstall_explain'] = 'Отключение всех компонентов плагина без физического удаления файлов';
$L['adm_opt_unpause'] = 'Продолжить выполнение';
$L['adm_opt_unpauseall'] = 'Продолжить выполнение всех';
$L['adm_opt_unpauseall_explain'] = 'Возобновление выполнения всех компонентов плагина';

$L['adm_opt_setup_missing'] = 'Ошибка: отсутствует файл настроек!';

$L['adm_sort_alphabet'] = 'По алфавиту';
$L['adm_sort_category'] = 'По категориям';

$L['adm_only_installed'] = 'Установленные';

/**
 * Tools Section
 */
$L['adm_listisempty'] = 'Элементы списка отсутствуют';

/**
 * Other Section
 * Cache Subsection
 */
$L['adm_delcacheitem'] = 'Элемент кэша удален';
$L['adm_internalcache'] = 'Внутренний кэш';
$L['adm_purgeall_done'] = 'Кэш очищен полностью';
$L['adm_diskcache'] = 'Дисковый кэш';
$L['adm_cache_showall'] = 'Отобразить все';

/**
 * Other Section
 * Log Subsection
 */
$L['adm_log'] = 'Системный протокол';
$L['adm_infos'] = 'Информация';
$L['adm_versiondclocks'] = 'Версии и таймеры';
$L['adm_checkcorethemes'] = 'Проверить файлы ядра и скинов';
$L['adm_checkcorenow'] = 'Проверить файлы ядра!';
$L['adm_checkingcore'] = 'Проверяю файлы ядра...';
$L['adm_checkthemes'] = 'Проверить наличие всех файлов в скине';
$L['adm_checkskin'] = 'Проверить TPL-файлы скина';
$L['adm_checkingskin'] = 'Проверяю скин...';
$L['adm_check_ok'] = 'Ok';
$L['adm_check_missing'] = 'Отсутствует';

/**
 * Other Section
 * Infos Subsection
 */
$L['adm_phpver'] = 'Версия PHP';
$L['adm_zendver'] = 'Версия Zend';
$L['adm_interface'] = 'Интерфейс веб-сервер / PHP';
$L['adm_os'] = 'Операционная система';
$L['adm_clocks'] = 'Таймеры';
$L['adm_time1'] = '#1 : Чистое время сервера';
$L['adm_time2'] = '#2 : Время относительно GMT, возвращаемое сервером';
$L['adm_time3'] = '#3 : Время относительно GMT + сдвиг сервера (Cotonti reference)';
$L['adm_time4'] = '#4 : Ваше местное время из личных установок';
$L['adm_help_versions'] = 'Измените часовой пояс сервера для корректной установки таймера #3.<br />
Таймер #4 зависит от установок часового пояса в вашем профиле.<br />
Таймеры #1 и #2 игнорируются системой.';

/**
 * Common Entries
 */
$L['adm_area'] = 'Зона';
$L['adm_clicktoedit'] = '(правка)';
$L['adm_confirm'] = 'Подтвердить';
$L['adm_done'] = 'Выполнено';
$L['adm_failed'] = 'Ошибка';
$L['adm_from'] = 'От';
$L['adm_more'] = 'Показать все...';
$L['adm_purgeall'] = 'Очистить все';
$L['adm_queue_unvalidated'] = 'Публикация поставлена в очередь';
$L['adm_queue_validated'] = 'Публикация утверждена';
$L['adm_required'] = '(обязательно)';
$L['adm_setby'] = 'Установлено';
$L['adm_to'] = 'Кому';
$L['adm_totalsize'] = 'Общий объем';
$L['adm_warnings'] = 'Предупреждения';

$L['editdeleteentries'] = 'Правка / удаление';
$L['viewdeleteentries'] = 'Просмотр / удаление';

$L['alreadyaddnewentry'] = 'Новая запись добавлена';
$L['alreadyupdatednewentry'] = 'Запись обновлена';
$L['alreadydeletednewentry'] = 'Запись удалена';

/**
 * Extra Fields (Common Entries for Pages & Structure & Users)
 */
$L['adm_extrafields'] = 'Экстраполя';
$L['adm_extrafield_added'] = 'Экстраполе добавлено';
$L['adm_extrafield_not_added'] = 'Ошибка! Экстраполе не добавлено';
$L['adm_extrafield_updated'] = 'Поле "%1$s" отредактировано';
$L['adm_extrafield_not_updated'] = 'Ошибка! Поле "%1$s" не отредактировано';
$L['adm_extrafield_removed'] = 'Экстраполе удалено';
$L['adm_extrafield_not_removed'] = 'Ошибка! Экстраполе не удалено';
$L['adm_extrafield_confirmdel'] = 'Вы действительно хотите удалить экстраполеполе? Все данные этого поля будут потеряны!';
$L['adm_extrafield_confirmupd'] = 'Вы действительно хотите редактировать экстраполеполе? Некоторые данные этого поля могут быть потеряны.';
$L['adm_extrafield_default'] = 'Значение по умолчанию';
$L['adm_extrafield_required'] = 'Обязательное';
$L['adm_extrafield_parse'] = 'Парсинг';
$L['adm_extrafield_enable'] = 'Включить';
$L['adm_extrafield_params'] = 'Параметры поля';

$L['extf_Name'] = 'Название поля';
$L['extf_Type'] = 'Тип поля';
$L['extf_Base_HTML'] = 'HTML-код поля';
$L['extf_Page_tags'] = 'Тэги';
$L['extf_Description'] = 'Описание поля (_TITLE)';

$L['adm_extrafield_new'] = 'Новое поле';
$L['adm_extrafield_noalter'] = 'Не добавлять новое поле в БД, только зарегистрировать как дополнительное';
$L['adm_extrafield_selectable_values'] = 'Значения для select, radio, checklistbox (через запятую)';
$L['adm_help_extrafield'] = 'HTML-код поля устанавливается в значение по умолчанию автоматически, если его очистить и обновить';

/**
 * Help messages that still don't work
 */
$L['adm_help_cache'] = 'Недоступно';
$L['adm_help_check1'] = 'Недоступно';
$L['adm_help_check2'] = 'Недоступно';
$L['adm_help_config']= 'Недоступно';

?>