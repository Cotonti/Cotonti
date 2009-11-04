<?php
/**
 * Russian Language File for Admin Area (admin.lang.php)
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Config Section
 */

$L['core_email'] = 'Настройки E-mail';// New in N-0.7.0
$L['core_comments'] = &$L['Comments'];
$L['core_forums'] = &$L['Forums'];
$L['core_lang'] = &$L['Language'];
$L['core_main'] = 'Настройки сайта';
$L['core_menus'] = &$L['Menus'];
$L['core_page'] = &$L['Pages'];
$L['core_parser'] = &$L['Parser'];
$L['core_pfs'] = &$L['PFS'];
$L['core_plug'] = &$L['Plugins'];
$L['core_pm'] = &$L['Private_Messages'];
$L['core_polls'] = &$L['Polls'];
$L['core_ratings'] = &$L['Ratings'];
$L['core_rss'] = &$L['Rss_feeds'];// New in N-0.7.0
$L['core_skin'] = &$L['Skins'];
$L['core_structure'] = &$L['Categories'];// New in N-0.7.0
$L['core_time'] = 'Время и дата';
$L['core_title'] = 'Заголовки (тэг &lt;title&gt;)';
$L['core_trash'] = &$L['Trashcan'];
$L['core_users'] = &$L['Users'];

/**
 * Config Section
 * E-mail Subsection
 */

$L['cfg_email_type'] = array('Тип отправки E-mail', ''); // New in N-0.7.0
$L['cfg_smtp_address'] = array('Адрес smtp сервера', 'Укажите если тип отправки E-mail выбран smtp'); // New in N-0.7.0
$L['cfg_smtp_port'] = array('Порт smtp сервера', 'Укажите если тип отправки E-mail выбран smtp'); // New in N-0.7.0
$L['cfg_smtp_login'] = array('Логин', 'Укажите если тип отправки E-mail выбран smtp'); // New in N-0.7.0
$L['cfg_smtp_password'] = array('Пароль', 'Укажите если тип отправки E-mail выбран smtp'); // New in N-0.7.0
$L['cfg_smtp_uses_ssl'] = array('Использовать SSL', 'Укажите если тип отправки E-mail выбран smtp'); // New in N-0.7.0

/**
 * Config Section
 * Comments Subsection
 */

$L['cfg_countcomments'] = array('Считать комментарии', 'Показывать количество комментариев рядом с иконкой');
$L['cfg_disable_comments'] = array('Отключить комментарии', 'Заблокировать использование и показ комментариев');
$L['cfg_expand_comments'] = array('Открыть комментарии', 'По умолчанию показывать комментарии на странице');	// New in N-0.0.2
$L['cfg_maxcommentsperpage'] = array('Макс. количество комментариев на странице', ' ');  // New in N-0.0.6
$L['cfg_commentsize'] = array('Макс. размер комментария', 'В байтах (0 - без ограничения размера). По умолчанию: 0');   // New in N-0.0.6

/**
 * Config Section
 * Forums Subsection
 */

$L['cfg_antibumpforums'] = array('Защита от &laquo;поднятия&raquo; сообщений (anti-bump)', 'Запретить пользователям создавать 2 сообщения подряд в теме');
$L['cfg_disable_forums'] = array('Отключить форумы', ' ');
$L['cfg_hideprivateforums'] = array('Скрывать приватные форумы', ' ');
$L['cfg_hottopictrigger'] = array('Количество сообщений для &laquo;популярной&raquo; темы', ' ');
$L['cfg_maxtopicsperpage'] = array('Максимальное количество тем на странице', ' ');
$L['cfg_mergeforumposts'] = array('Объединять сообщения', 'Объединять идущие подряд сообщения одного пользователя (антибамповая защита должна быть отключена)');	// New in N-0.1.0
$L['cfg_mergetimeout'] = array('Время ожидания для объединения сообщений', 'Последовательно опубликованные сообщения одного пользователя не будут объединены при превышении указанного времени (в часах), требует включения установки \'Объединять сообщения\' (0 для отключения)');	// New in N-0.1.0
$L['cfg_maxpostsperpage'] = array('Макс. количество сообщений на странице', ' '); // New in N-0.0.6

/**
 * Config Section
 * Lang Subsection
 */

$L['cfg_forcedefaultlang'] = array('Принудительная установка языка по умолчанию для всех пользователей', ' ');

/**
 * Config Section
 * Main Subsection
 */

$L['cfg_adminemail'] = array('E-mail администратора сайта', 'Обязательно!');
$L['cfg_turnajax'] = array('Включить Ajax', 'Работает только если jQuery включен');
$L['cfg_cache'] = array('Внутренний кэш', 'Повышает производительность работы сайта');
$L['cfg_clustermode'] = array('Серверный кластер', 'Выберите Да, если используется кластерная система балансировки нагрузок.');
$L['cfg_cookiedomain'] = array('Домен для cookies', 'По умолчанию пусто');
$L['cfg_cookielifetime'] = array('Срок действия cookies', 'В секундах');
$L['cfg_cookiepath'] = array('Путь для cookies', 'По умолчанию пусто');
$L['cfg_devmode'] = array('Режим отладки', 'Только для отладки под localhost');
$L['cfg_disablehitstats'] = array('Отключить статистику', 'Рефереры и хиты за день');
$L['cfg_gzip'] = array('Gzip', 'Gzip-сжатие для исходящего HTML-кода');
$L['cfg_hostip'] = array('IP-адрес сервера', 'Необязательно');
$L['cfg_jquery'] = array('Включить jQuery', ' ');	// New in N-0.0.1
$L['cfg_maintenance'] = array('Режим обслуживания', 'Доступа к сайту разрешен только администраторам'); // New in N-0.0.2
$L['cfg_maintenancereason'] = array('Причина режима обслуживания', 'Коротко опишите почему сайт находится в режиме обслуживания');	// New in N-0.0.2
$L['cfg_shieldenabled'] = array('Включить защиту', 'Защита против спама и хаммеринга');
$L['cfg_shieldtadjust'] = array('Настройка таймеров защиты (в %)', 'Чем выше, тем сильнее защита против спама');
$L['cfg_shieldzhammer'] = array('Анти-хаммер после * хитов', 'Чем меньше, тем короче срок автоблокировки пользователя');
$L['cfg_redirbkonlogin'] = array('Возврат после авторизации', 'Вернуться на страницу, посещённую перед авторизацией');	// New in N-0.6.1
$L['cfg_redirbkonlogout'] = array('Возврат после выхода', 'Вернуться на страницу, посещённую перед выходом');	// New in N-0.6.1

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
 * Page Subsection
 */

$L['cfg_allowphp_pages'] = array('Разрешить страницы на PHP', 'Внимание: исполнение PHP кода в страницах может стать причиной некорректной работы или взлома сайта!');
$L['cfg_autovalidate'] = array('Автоматическое утверждение страниц', 'Автоматически утверждать публикацию страниц, созданных пользователем с правом администрирования раздела'); // New in N-0.0.2
$L['cfg_count_admin'] = array('Считать посещения администраторов', 'Включить посещения администраторов в статистику посещаемости сайта');	// New in N-0.0.1
$L['cfg_disable_page'] = array('Отключить страницы', ' ');
$L['cfg_maxrowsperpage'] = array('Макс. количество записей на страницу списка', ' ');
$L['cfg_maxlistsperpage'] = array('Макс. количество категорий на странице', ' '); // New in N-0.0.6

/**
 * Config Section
 * Parser Subsection
 */

$L['cfg_parsebbcodecom'] = array('Парсинг BBCode в комментариях и личных сообщениях', ' ');
$L['cfg_parsebbcodeforums'] = array('Парсинг BBCode в форумах', ' ');
$L['cfg_parsebbcodepages'] = array('Парсинг BBCode в страницах', ' ');
$L['cfg_parsebbcodeusertext'] = array('Парсинг BBCode в подписях пользователей', ' ');
$L['cfg_parser_cache'] = array('Включить HTML-кэш', ' ');	// New in N-0.0.1
$L['cfg_parser_custom'] = array('Включить собственный парсер', ' ');	// New in N-0.0.1
$L['cfg_parser_disable'] = array('Отключить стандартный парсер', ' ');	// New in N-0.0.3
$L['cfg_parsesmiliescom'] = array('Парсинг смайликов в комментариях и личных сообщениях', ' ');
$L['cfg_parsesmiliesforums'] = array('Парсинг смайликов в форумах', ' ');
$L['cfg_parsesmiliespages'] = array('Парсинг смайликов в страницах', ' ');
$L['cfg_parsesmiliesusertext'] = array('Парсинг смайликов в подписях пользователей', ' ');

/**
 * Config Section
 * PFS Subsection
 */

$L['cfg_disable_pfs'] = array('Отключить &laquo;'.$L['PFS'].'&raquo;', ' ');
$L['cfg_maxpfsperpage'] = array('Макс. количество элементов на странице', ' ');
$L['cfg_pfsfilecheck'] = array('Проверка файлов', 'Проверять загружаемые файлы (&laquo;'.$L['PFS'].'&raquo; и профиль) на соответствие их формата используемому расширению. Рекомендуется включить в целях безопасности.');	// New in N-0.0.2
$L['cfg_pfsnomimepass'] = array('Игнорировать MIME-типы', 'Разрешить закачку файлов, MIME-тип которых не указан в конфигурации.');	// New in N-0.0.2
$L['cfg_pfstimename'] = array('Имена файлов на основе шаблона времени', 'Генерировать имена файлов по шаблону времени. По умолчанию используется маска ИМЯФАЙЛА_UDERID.');	// New in N-0.0.2
$L['cfg_pfsuserfolder'] = array('Режим хранения по каталогам', 'Пользовательские файлы будут храниться в каталогах /datas/users/USERID/ вместо /datas/users/ и добавления USERID к имени файла. Устанавливается <u>только при начальной настройке сайта</u>. Менять значение после первой загрузки любого файла не рекомендуется!');
$L['cfg_pfs_winclose'] = array('Закрывать всплывающее окно после вставки ббкода');
$L['cfg_th_amode'] = array('Метод создания миниатюр изображений (thumbnails)', ' ');
$L['cfg_th_border'] = array('Ширина рамки миниатюры, px', 'По умолчанию: 4px');
$L['cfg_th_colorbg'] = array('Цвет рамки миниатюры', 'По умолчанию: #000000');
$L['cfg_th_colortext'] = array('Цвет текста миниатюры', 'По умолчанию: #FFFFFF');
$L['cfg_th_dimpriority'] = array('Приоритет размеров миниатюр (thumbnails)', ' ');
$L['cfg_th_jpeg_quality'] = array('Коэффициент JPEG-сжатия миниатюры', 'По умолчанию: 85');
$L['cfg_th_keepratio'] = array('Сохранять пропорции изображения в миниатюре', ' ');
$L['cfg_th_textsize'] = array('Размер шрифта миниатюры', ' ');
$L['cfg_th_x'] = array('Ширина миниатюры, px', 'По умолчанию: 112px');
$L['cfg_th_y'] = array('Высота миниатюры, px', 'По умолчанию: 84px (рекомендуется: ширина x 0.75)');

/**
 * Config Section
 * Plugins Subsection
 */

$L['cfg_disable_plug'] = array('Отключить плагины', ' ');

/**
 * Config Section
 * Private Messages Subsection
 */

$L['cfg_disable_pm'] = array('Отключить личные сообщения', ' ');
$L['cfg_pm_allownotifications'] = array('E-mail уведомления', 'Отсылать на пользовательский e-mail уведомления о поступивших личных сообщениях');
$L['cfg_pm_maxsize'] = array('Максимальное количество символов в личном сообщении', 'По умолчанию: 10000 символов');
$L['cfg_maxpmperpage'] = array('Макс. количество сообщений на странице', ' '); // New in N-0.0.6

/**
 * Config Section
 * Polls Subsection
 */

$L['cfg_del_dup_options'] = array('Принудительное удаление дублирующихся ответов', 'Удалять дублирующийся ответ даже если он уже внесен в базу данных');	// New in N-0.0.2
$L['cfg_disable_polls'] = array('Отключить опросы', ' ');
$L['cfg_ip_id_polls'] = array('Способ запоминания голоса', ' ');	// New in N-0.0.2
$L['cfg_max_options_polls'] = array('Максимальное количество вариантов ответа', 'Лишние варианты будут автоматически удаляться при привышении лимита');	// New in N-0.0.2

/**
 * Config Section
 * Ratings Subsection
 */

$L['cfg_disable_ratings'] = array('Отключить рейтинги', ' ');
$L['cfg_ratings_allowchange'] = array('Разрешить изменение рейтинга', 'Разрешить пользователям изменять ранее выставленный рейтинг');	// New in N-0.0.2

/**
 * Config Section
 * RSS Subsection
 */

$L['cfg_disable_rss'] = array('Отключить RSS каналы', ''); // New in N-0.7.0
$L['cfg_rss_timetolive'] = array('Как часто обновлять RSS кеш', 'В секундах'); // New in N-0.7.0
$L['cfg_rss_maxitems'] = array('Макс. количество элементов в RSS канале', ' '); // New in N-0.7.0
$L['cfg_rss_charset'] = array('Кодировка RSS каналов', 'Набор символов (кодовая страница)'); // New in N-0.7.0

/**
 * Config Section
 * Skins Subsection
 */

$L['cfg_charset'] = array('Набор символов (кодовая страница)', ' ');
$L['cfg_disablesysinfos'] = array('Отключить время создания страницы', 'В footer.tpl');
$L['cfg_doctypeid'] = array('Тип документа', '&lt;!DOCTYPE&gt; в HTML-разметке');
$L['cfg_forcedefaultskin'] = array('Принудительная установка скина по умолчанию для всех пользователей', ' ');
$L['cfg_homebreadcrumb'] = array('Ссылка на главную страницу в &laquo;навигационной цепочке&raquo;', 'Установить ссылку на главную страницу в начале &laquo;навигационной цепочки&raquo;');
$L['cfg_keepcrbottom'] = array('Оставить копирайт в тэге {FOOTER_BOTTOMLINE}', 'В footer.tpl');
$L['cfg_metakeywords'] = array('Ключевые слова', 'Указать через запятую ключевые слова для поисковых систем');
$L['cfg_separator'] = array('Разделитель', 'По умолчанию: &gt;');
$L['cfg_showsqlstats'] = array('Показывать статистику SQL-запросов', 'В footer.tpl');

/**
 * Config Section
 * Time Subsection
 */

$L['cfg_dateformat'] = array('Полный формат даты', 'По умолчанию Y-m-d H:i');
$L['cfg_formatmonthday'] = array('Укороченный формат даты', 'По умолчанию m-d');
$L['cfg_formatyearmonthday'] = array('Обычный формат даты', 'По умолчанию Y-m-d');
$L['cfg_formatmonthdayhourmin'] = array('Формат даты для форумов', 'По умолчанию m-d H:i');
$L['cfg_servertimezone'] = array('Часовой пояс сервера', 'Относительно GMT+00');
$L['cfg_defaulttimezone'] = array('Часовой пояс по умолчанию', 'Для гостей и при регистрации, от -12 до +12');
$L['cfg_timedout'] = array('Задержка ожидания в секундах', 'По истечении данного срока пользователь считается покинувшим сайт');

/**
 * Config Section
 * Title Subsection
 */

$L['cfg_maintitle'] = array('Название сайта', 'Обязательно');
$L['cfg_subtitle'] = array('Описание сайта', 'Необязательно');
$L['cfg_title_forum_editpost'] = array('Форумы - правка', 'Опции: {FORUM}, {SECTION}, {EDIT}');
$L['cfg_title_forum_main'] = array('Форумы - главная страница', 'Опции: {FORUM}');
$L['cfg_title_forum_newtopic'] = array('Форумы - новая тема', 'Опции: {FORUM}, {SECTION}, {NEWTOPIC}');
$L['cfg_title_forum_posts'] = array('Форумы - сообщения', 'Опции: {FORUM}, {TITLE}');
$L['cfg_title_forum_topics'] = array('Форумы - темы', 'Опции: {FORUM}, {SECTION}');
$L['cfg_title_header'] = array('Основной заголовок', 'Опции: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}');
$L['cfg_title_header_index'] = array('Заголовок главной страницы', 'Опции: {MAINTITLE}, {DESCRIPTION}, {SUBTITLE}');
$L['cfg_title_list'] = array('Заголовок раздела', 'Опции: {TITLE}');
$L['cfg_title_page'] = array('Заголовок страницы', 'Опции: {TITLE}, {CATEGORY}');
$L['cfg_title_pfs'] = array($L['PFS'], 'Опции: {PFS}');
$L['cfg_title_pm_main'] = array('Личные сообщения', 'Опции: {PM}, {INBOX}, {ARCHIVES}, {SENTBOX}');
$L['cfg_title_pm_send'] = array('Отправка личных сообщений', 'Опции: {PM}, {SEND_NEW}');
$L['cfg_title_users_details'] = array('Пользователи - просмотр профиля', 'Опции: {USER}, {NAME}');
$L['cfg_title_users_edit'] = array('Пользователи - редактирование пользователя', 'Опции: {EDIT}, {NAME}');
$L['cfg_title_users_main'] = array('Пользователи - главная', 'Опции: {USERS}');
$L['cfg_title_users_profile'] = array('Пользователи - редактирование профиля', 'Опции: {PROFILE}, {NAME}');

/**
 * Config Section
 * Trash Subsection
 */

$L['cfg_trash_comment'] = array('Удалять в корзину комментарии', ' ');
$L['cfg_trash_forum'] = array('Удалять в корзину форумы', ' ');
$L['cfg_trash_page'] = array('Удалять в корзину страницы', ' ');
$L['cfg_trash_pm'] = array('Удалять в корзину личные сообщения', ' ');
$L['cfg_trash_prunedelay'] = array('Очищать корзину через', 'дней (0 - отключить очистку корзины)');
$L['cfg_trash_user'] = array('Удалять в корзину учетные записи пользователей', ' ');

/**
 * Config Section
 * Users Subsection
 */

$L['cfg_av_maxsize'] = array('Максимальный размер аватара, байт', 'По умолчанию: 8000 байт');
$L['cfg_av_maxx'] = array('Максимальная ширина аватара, px', 'По умолчанию: 64px');
$L['cfg_av_maxy'] = array('Максимальная высота аватара, px', 'По умолчанию: 64px');
$L['cfg_disablereg'] = array('Отключить регистрацию', 'Запретить регистрацию новых пользователей');
$L['cfg_disablewhosonline'] = array('Отключить статистику &laquo;Кто онлайн&raquo;', 'Включается автоматически при включении защиты');
$L['cfg_maxusersperpage'] = array('Максимальное количество записей на страницу в списке пользователей', ' ');
$L['cfg_ph_maxsize'] = array('Максимальный размер фото, байт', 'По умолчанию 8000 байт');
$L['cfg_ph_maxx'] = array('Максимальная ширина фото', 'По умолчанию 96px');
$L['cfg_ph_maxy'] = array('Максимальная высота фото', 'По умолчанию 96px');
$L['cfg_regnoactivation'] = array('Отменить проверку e-mail при регистрации', 'По причине безопасности рекомендуется &laquo;Нет&raquo;!');
$L['cfg_regrequireadmin'] = array('Утверждение новых учетных записей администратором', ' ');
$L['cfg_sig_maxsize'] = array('Максимальная размер файла в подписи, байт', 'По умолчанию: 50000 байт');
$L['cfg_sig_maxx'] = array('Максимальная ширина подписи, px', 'По умолчанию 468px');
$L['cfg_sig_maxy'] = array('Максимальная высота подписи, px', 'По умолчанию 60px');
$L['cfg_user_email_noprotection'] = array('Выключить защиту смены e-mail с паролем', 'По причине безопасности рекомендуется &laquo;Нет&raquo;!');
$L['cfg_useremailchange'] = array('Разрешить пользователям изменять свой e-mail', 'По причине безопасности рекомендуется &laquo;Нет&raquo;!');
$L['cfg_usertextimg'] = array('Разрешить изображения и HTML-код в подписях пользователей', 'По причине безопасности рекомендуется &laquo;Нет&raquo;!');
$L['cfg_usertextmax'] = array('Максимальная длина подписи, символов', 'По умолчанию: 300 символов');

/**
  * Page Section
 */

$L['addnewentry'] = 'Добавить новую запись';
$L['adm_queue_deleted'] = 'Страница удалена в корзину';
$L['adm_valqueue'] = 'В очереди на утверждение';
$L['adm_structure'] = 'Структура страниц (категории)';
$L['adm_extrafields_desc'] = 'Создание / правка дополнительных полей';
$L['adm_sortingorder'] = 'Порядок сортировки по умолчанию в категории';
$L['adm_showall'] = 'Показать все';
$L['adm_help_page'] = 'Страницы категории &laquo;system&raquo; не отображаются в списках страниц и являются отдельными, самостоятельными страницами'; // Edit in N-0.7.0 	Пожалуйста не нужно переводить слово "system" в этой строке (Dayver)
$L['adm_fileyesno'] = 'Файл (да/нет)';
$L['adm_fileurl'] = 'URL файла';
$L['adm_filecount'] = 'Количество загрузок';
$L['adm_filesize'] = 'Размер файла';

/**
 * Page Section
 * Extrafields Subsection
 */

$L['adm_help_pages_extrafield'] = 'HTML-код поля установится в значение по умолчанию автоматически, если его очистить и обновить<br /><br />
<b>Новые тэги в tpl-файлах:</b><br /><br />
page.tpl: {PAGE_XXXXX}, {PAGE_XXXXX_TITLE}<br /><br />
page.add.tpl: {PAGEADD_FORM_XXXXX}, {PAGEADD_FORM_XXXXX_TITLE}<br /><br />
page.edit.tpl: {PAGEEDIT_FORM_XXXXX}, {PAGEEDIT_FORM_XXXXX_TITLE}<br /><br />
list.tpl: {LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}<br />';

/**
  * Structure Section
 */

$L['adm_tpl_mode'] = 'Установка шаблона';
$L['adm_tpl_empty'] = 'По умолчанию';
$L['adm_tpl_forced'] = 'Как';
$L['adm_tpl_parent'] = 'Как родительская категория';
$L['adm_enablecomments'] = 'Включить комментарии';	// New in N-0.1.0
$L['adm_enableratings'] = 'Включить рейтинги';	// New in N-0.1.0
$L['adm_help_structure'] = 'Страницы категории &laquo;system&raquo; не отображаются в списках страниц и являются отдельными, самостоятельными страницами'; // Added in N-0.7.0 	Пожалуйста не нужно переводить слово "system" в этой строке (Dayver)

/**
 * Structure Section
 * Extrafields Subsection
 */

$L['adm_help_structure_extrafield'] = 'HTML-код поля установится в значение по умолчанию автоматически, если его очистить и обновить<br /><br />
<b>Новые тэги в tpl-файлах:</b><br /><br />
<u>list.tpl:</u><br /><br />
&nbsp;&nbsp;&nbsp;{LIST_XXXXX}, {LIST_XXXXX_TITLE}<br /><br />
<u>list.group.tpl:</u><br /><br />
&nbsp;&nbsp;&nbsp;{LIST_XXXXX}, {LIST_XXXXX_TITLE}<br /><br />
<u>admin.structure.inc.tpl :</u><br /><br />
&nbsp;&nbsp;&nbsp;&lt;!-- BEGIN: OPTIONS --&gt; {ADMIN_STRUCTURE_XXXXX}, {ADMIN_STRUCTURE_XXXXX_TITLE} &lt;!-- END: OPTIONS --&gt;<br /><br />
&nbsp;&nbsp;&nbsp;&lt;!-- BEGIN: DEFULT --&gt; {ADMIN_STRUCTURE_FORMADD_XXXXX}, {ADMIN_STRUCTURE_FORMADD_XXXXX_TITLE} &lt;!-- END: DEFULT --&gt;<br /><br />
<br />';

/**
 * Forums Section
 */

$L['adm_forum_structure'] = 'Структура форумов (разделы)';
$L['adm_forum_emptytitle'] = 'Ошибка: пустой заголовок';	// New in N-0.1.0

/**
  * Forums Section
  * Structure Subsection
 */

$L['adm_defstate'] = 'По умолчанию';
$L['adm_defstate_0'] = 'Свернут';
$L['adm_defstate_1'] = 'Развернут';

/**
  * Forums Section
  * Forum Edit Subsection
 */

$L['adm_forums_master'] = 'Родительский раздел';	// New in N-0.0.1
$L['adm_diplaysignatures'] = 'Показывать подписи';
$L['adm_enablebbcodes'] = 'Разрешить BBCodes';
$L['adm_enablesmilies'] = 'Разрешить смайлики';
$L['adm_enableprvtopics'] = 'Разрешить приватные темы';
$L['adm_enableviewers'] = 'Включить отображение просматривающих раздел';  // New in N-0.0.2
$L['adm_enablepolls'] = 'Включить опросы';  // New in N-0.0.2
$L['adm_countposts'] = 'Считать сообщения';
$L['adm_autoprune'] = 'Автоочистка тем через * дней';
$L['adm_postcounters'] = 'Проверка счетчиков';

/**
 * Users Section
 */

$L['adm_rightspergroup'] = 'Права групп';
$L['adm_maxsizesingle'] = 'Максимальный размер одного файла в разделе &laquo;'.$L['PFS'].'&raquo; (Кб)';
$L['adm_maxsizeallpfs'] = 'Максимальный размер всех файлов в разделе &laquo;'.$L['PFS'].'&raquo; (Кб)';
$L['adm_copyrightsfrom'] = 'Установить права как в группе';
$L['adm_rights_maintenance'] = 'Разрешить авторизацию при включенном режиме обслуживания'; // New in N-0.0.2

/**
 * Users Section
 * Extrafields Subsection
 */

$L['adm_help_users_extrafield'] = 'Поле &laquo;Базовый HTML&raquo; устанавливается в значение по умолчанию автоматически, если его очистить и обновить.<br /><br />
<b>Новые тэги в tpl-файлах:</b><br /><br />
users.profile.tpl: {USERS_PROFILE_XXXXX}, {USERS_PROFILE_XXXXX_TITLE}<br /><br />
users.edit.tpl: {USERS_EDIT_XXXXX}, {USERS_EDIT_XXXXX_TITLE}<br /><br />
users.details.tpl: {USERS_DETAILS_XXXXX}, {USERS_DETAILS_XXXXX_TITLE}<br /><br />
user.register.tpl: {USERS_REGISTER_XXXXX}, {USERS_REGISTER_XXXXX_TITLE}<br /><br />
forums.posts.tpl: {FORUMS_POSTS_ROW_USERXXXXX}, {FORUMS_POSTS_ROW_USERXXXXX_TITLE}<br />';

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
$L['adm_installed'] = 'Установлен';	// New in N-0.0.6
$L['adm_notinstalled'] = 'Не установлен';

$L['adm_plugsetup'] = 'Настройки плагина';	// New in N-0.0.6
$L['adm_override_guests'] = 'Системная блокировка: незарегистрированным и неактивированным пользователям доступ к администрированию запрещен';	// New in N-0.0.6
$L['adm_override_banned'] = 'Системная блокировка: учетная запись заблокирована';	// New in N-0.0.6
$L['adm_override_admins'] = 'Системная блокировка: администраторы';	// New in N-0.0.6

$L['adm_opt_installall'] = 'Установить';
$L['adm_opt_installall_explain'] = 'Установка или сброс всех компонентов плагина в значения по умолчанию';
$L['adm_opt_uninstallall'] = 'Удалить';
$L['adm_opt_uninstallall_explain'] = 'Отключение всех компонентов плагина без физического удаления файлов';
$L['adm_opt_pauseall'] = 'Приостановить';
$L['adm_opt_pauseall_explain'] = 'Остановка выполнения всех компонентов плагина';
$L['adm_opt_unpauseall'] = 'Продолжить выполнение';
$L['adm_opt_unpauseall_explain'] = 'Возобновление выполнения всех компонентов плагина';

$L['adm_opt_setoption_warn'] = 'Найти настройки для этого плагина. Вы хотите установить с сохранением этих настроек?'; // New in N-0.0.2
$L['adm_opt_uninstall_warn'] = 'Удалить плагин с сохранением существующих настроек и прав доступа';	// New in N-0.0.2
$L['adm_opt_setup_missing'] = 'Ошибка: отсутствует файл настроек!';	// New in N-0.0.6

$L['adm_pluginstall_msg01'] = 'Деинсталляция плагина...';	// New in N-0.0.6
$L['adm_pluginstall_msg02'] = 'Удаление настроек плагина...';	// New in N-0.0.6
$L['adm_pluginstall_msg03'] = 'Поиск файла конфигурации...';	// New in N-0.0.6
$L['adm_pluginstall_msg04'] = 'Поиск частей...';	// New in N-0.0.6
$L['adm_pluginstall_msg05'] = 'Установка частей...';	// New in N-0.0.6
$L['adm_pluginstall_msg06'] = 'Поиск настроек в файле конфигурации...';	// New in N-0.0.6
$L['adm_pluginstall_msg07'] = 'Не найдено! Установка прервана!';	// New in N-0.0.6
$L['adm_pluginstall_msg08'] = 'Удаление прав пользователей для плагина...';	// New in N-0.0.6
$L['adm_pluginstall_msg09'] = 'Добавление прав для групп ользователей...';	// New in N-0.0.6
$L['adm_pluginstall_msg10'] = 'Сброс прав для всех пользователей...';	// New in N-0.0.6
$L['adm_pluginstall_msg11'] = 'Выполнение деинсталлятора...';	// New in N-0.0.6

/**
 * Tools Section
 */

$L['adm_listisempty'] = 'Элементы списка отсутствуют';

/**
  * TrashCan Section
 */

$L['adm_help_trashcan'] = 'Записи, удаленные пользователями и модераторами<br />
- удалить окончательно: окончательно удалить запись из базы данных<br />
- восстановить: вернуть запись в базу данных<br />
<b>Внимание:</b><br />
- восстанавливая тему форума, вы восстанавливаете и все сообщения в ней<br />
- восстанавливая сообщение в удаленной теме, вы восстанавливаете саму тему и (если это возможно) все дочерние сообщения';

/**
 * Other Section
 * Comments Subsection
 */

$L['adm_comm_already_del'] = 'Комментарий удален';	// New in N-0.0.2

/**
 * Other Section
 * PFS Subsection
 */

$L['adm_gd'] = 'Графическая библиотека GD';
$L['adm_allpfs'] = 'Разделы &laquo;'.$L['PFS'].'&raquo; всех пользователей';
$L['adm_allfiles'] = 'Все файлы';
$L['adm_thumbnails'] = 'Миниатюры';
$L['adm_orphandbentries'] = 'Потерянные записи БД';
$L['adm_orphanfiles'] = 'Потерянные файлы';
$L['adm_delallthumbs'] = 'Удалить все миниатюры';
$L['adm_rebuildallthumbs']= 'Удалить и сгенерировать все миниатюры';
$L['adm_help_allpfs'] = 'Разделы &laquo;'.$L['PFS'].'&raquo; всех зарегистрированных пользователей';
$L['adm_nogd'] = 'Графическая библиотека GD не поддерживается данным хостом. Создание миниатюр для изображений невозможно. Установите переменную &laquo;Метод создания миниатюр&raquo; ('.$L['Configuration'].' &gt; '.$L['PFS'].') в значение &laquo;'.$L['Disabled'].'&raquo;';

/**
 * Other Section
 * Polls Subsection
 */

$L['adm_help_polls'] = 'При создании опроса пустые ответы не учитываются и автоматически удаляются.<br />После создания опроса не рекомендуется редактировать его, так это может повлиять на ход голосования.';
$L['adm_polls_forumpolls'] = 'Опросы в форумах (последние вверху) :';	// New in N-0.0.1
$L['adm_polls_indexpolls'] = 'Опросы на главной (последние вверху) :';	// New in N-0.0.1
$L['adm_polls_msg916_bump'] = 'Опрос поднят!';	// New in N-0.0.3
$L['adm_polls_msg916_deleted'] = 'Опрос удален!';	// New in N-0.0.3
$L['adm_polls_msg916_reset'] = 'Результаты опроса обнулены!';	// New in N-0.0.3
$L['adm_polls_on_page'] = 'на странице';	// New in N-0.0.2
$L['adm_polls_polltopic'] = 'Опрос';	// New in N-0.0.1

/**
 * Other Section
 * PM Subsection
 */

$L['adm_pm_totaldb'] = 'Личных сообщений в базе данных';
$L['adm_pm_totalsent'] = 'Всего отправлено личных сообщений';

/**
 * Other Section
 * Ratings Subsection
 */

$L['adm_ratings_already_del'] = 'Рейтинг удален';	// New in N-0.0.3
$L['adm_ratings_totalitems'] = 'Рейтингованных страниц';
$L['adm_ratings_totalvotes'] = 'Всего голосов';
$L['adm_help_ratings'] = 'Для обнуления рейтинга просто удалите его. Рейтинг будет создан заново при отправке первой оценки.';

/**
 * Other Section
 * Cache Subsection
 */

$L['adm_delcacheitem'] = 'Элемент кэша удален';	// New in N-0.0.2
$L['adm_internalcache'] = 'Внутренний кэш';
$L['adm_purgeall_done'] = 'Кэш очищен полностью';	// New in N-0.0.2
$L['adm_diskcache'] = 'Дисковый кэш';	// New in N-0.6.1

/**
 * Other Section
 * BBCode Subsection
 */

$L['adm_bbcode'] = 'BBCode';
$L['adm_bbcodes'] = 'BBCodes';
$L['adm_bbcodes_added'] = 'Новый BBCode успешно добавлен.';
$L['adm_bbcodes_clearcache'] = 'Очистить HTML-кэш';
$L['adm_bbcodes_clearcache_confirm'] = 'Это очистит кэш всех страниц и сообщений. Продолжить?';
$L['adm_bbcodes_clearcache_done'] = 'HTML-кэш очищен.';
$L['adm_bbcodes_confirm'] = 'Удалить данный BBCode?';
$L['adm_bbcodes_container'] = 'Контейнер';
$L['adm_bbcodes_mode'] = 'Режим';
$L['adm_bbcodes_new'] = 'Новый BBCode';
$L['adm_bbcodes_pattern'] = 'Шаблон';
$L['adm_bbcodes_postrender'] = 'Пост-рендер';
$L['adm_bbcodes_priority'] = 'Приоритет';
$L['adm_bbcodes_removed'] = 'BBCode удален.';
$L['adm_bbcodes_replacement'] = 'Замена';
$L['adm_bbcodes_updated'] = 'BBCode обновлен.';
$L['adm_help_bbcodes'] = <<<HTM
<ul>
<li><strong>Имя</strong> - Название BBcode (только буквы латинского алфавита, цифры и подчеркивание)</li>
<li><strong>Режим</strong> - Режим парсинга, один из: &laquo;str&raquo; (str_replace), &laquo;ereg&raquo; (eregi_replace), &laquo;pcre&raquo; (preg_replace) или &laquo;callback&raquo; (preg_replace_callback)</li>
<li><strong>Шаблон</strong> - Строка BBCode или регулярное выражение</li>
<li><strong>Замена</strong> - Строка замены, регулярная замена или тело функции обратного вызова</li>
<li><strong>Контейнер</strong> - Является ли BBCode контейнером (например, [bbcode]Какой-то текст[/bbcode])</li>
<li><strong>Приоритет</strong> - Приоритет BBCode от 0 до 255. BBCode с меньшим приоритетом обрабатывается в первую очередь, стандартный средний приоритет -- 128.</li>
<li><strong>Плагин</strong> - Код плагина/части, которой принадлежит BBCode. Только для плагинов.</li>
<li><strong>Пост-рендер</strong> - Применять BBCode к сформированному HTML-кэшу. Используйте только если ваш callback-код делает какие-то вычисления на каждом запросе.</li>
</ul>
HTM;

/**
 * Other Section
 * URLs Subsection
 */

$L['adm_urls'] = 'Ссылки';
$L['adm_urls_area'] = 'Модуль';
$L['adm_urls_error_dat'] = 'Ошибка: datas/urltrans.dat недоступен для записи!';
$L['adm_urls_format'] = 'Формат';
$L['adm_urls_htaccess'] = 'Перезаписать .htaccess?';
$L['adm_urls_new'] = 'Новое правило';
$L['adm_urls_parameters'] = 'Параметры';
$L['adm_urls_rules'] = 'Правила преобразования URL';
$L['adm_urls_save'] = 'Сохранить';
$L['adm_urls_your'] = 'Ваш';
$L['adm_urls_callbacks'] = 'Правило содержит callback-вызовы';
$L['adm_urls_errors'] = 'Вам придется добавить опции rewrite для них вручную.';
$L['adm_help_urls'] = 'На этой странице вы можете настроить формат ссылок, используя простые правила преобразования. Удостоверьтесь в корректности и отсутствии повторяющихся правил. Не используйте пробелы, символы табуляции и прочие специальные символы в правилах. Разделы и параметры объяснены ниже.
<ol>
<li><strong>Модуль</strong> - название скрипта, к которому относится правило. Метасимвол (*) означает &laquo;любой скрипт&raquo;.</li>
<li><strong>Параметры</strong> - условие, проверяемое на параметрах ссылки. Это строка, содержащая пары имя-значение, разделенные символом &amp; и символом = между именем и значением. Знак вопроса (?) в начале строки ставить не следует. Если вы задаете здесь некоторую переменную, то для работы правила она должна присутствовать в параметрах ссылки. Символ * означает любое значение, одиночное значение или список возможных значений, разделенных вертикальной чертой (|). Все значения должны быть URL-кодированы. <em>Пример: name=Val|Josh&amp;id=124&amp;page=*</em>.</li>
<li><strong>Формат</strong> задает формат формирования ссылки. Это строка, содержащая специальные последовательности, заменяемые их значениями. Обычная последовательность выглядит как {$name}, где &quot;name&quot; - имя параметра ссылки (GET-переменной), значение которой будет вставлено вместо этой последовательности. Есть несколько специальных последовательностей, не содержащихся в параметрах ссылки (&quot;query string&quot;):
	<ul>
		<li><em>{$_area}</em> - имя скрипта;</li>
		<li><em>{$_host}</em> - имя хоста из главной ссылки сайта;</li>
		<li><em>{$_rhost}</em> - имя хоста из текущего HTTP-запроса;</li>
		<li><em>{$_path}</em> - путь сайта относительно корня сервера, / если сайт в корне.</li>
	</ul>
Вы можете также использовать параметризированные поддомены, если укажете абсолютную ссылку вида: <em>http://{$c}.site.com/{$al}.html</em>. На данный мамент поддомены поддерживаются только для серверов Apache.</li>
<li><strong>Новое правило</strong> - добавляет новое правило в таблицу.</li>
<li><strong>Порядок</strong> - помните, что порядок следования правил имеет значение. Алгоритм преобразования ссылок ищет подходящее правило следующим образом: сначала он ищет правила для текущего скрипта, потом он пробует найти <em>первое</em> правило, подходящее по параметрам; если подходящее правило не найдено, происходит возврат к универсальным правилам (модуль *) и первое подходящее правило ищется там. Рекомендуется правило по умолчанию (с * модулем и * параметрами) ставить последнем среди всех правил для *-модуля или даже последним в таблице.<br />
Вы можете поменять порядок правил простым перетаскиванием нужных строк в таблице. Рекомендуется сохранить новые правила перед изменением порядка, иначе перетаскивание для них работать не будет.</li>
<li><strong>Query String</strong> - это то, что вы видите в большинстве ссылок после знака вопроса. Она используется для передачи остальных GET-параметров, которые вы не использовали при составлении Формата, и автоматически присоединяется к ссылке в таком случае.</li>
<li><strong>Сохранить</strong> - эта кнопка сохранит правила, и изменения вступят в силу немедленно. Такжы будет изменен ваш .htaccess (если возможно), и вам будут предоставлены директивы для .htaccess/IsapiRewrite4.ini/nginx.conf (в зависимости от используемого сервера).</li>
</ol>';

/**
 * Other Section
 * Banlist Subsection
 */

$L['adm_ipmask'] = 'IP-маска';
$L['adm_emailmask'] = 'E-mail маска';
$L['adm_neverexpire'] = 'Без срока действия';
$L['adm_help_banlist'] = 'Образцы IP-масок: 194.31.13.41, 194.31.13.*, 194.31.*.*, 194.*.*.*<br />Образцы e-mail масок: @hotmail.com, @yahoo (шаблоны (wildcards) не поддерживаются)<br />Запись может содержать одну IP-маску, одну e-mail маску или обе маски.<br />IP-адреса фильтруются для всех без исключения страниц, e-mail маски применяются только при регистрации пользователей.';

$L['adm_searchthisuser'] = 'Поиск данного IP-адреса в базе данных пользователей';
$L['adm_dnsrecord'] = 'DNS-запись для данного адреса';

$L['alreadyaddnewentry'] = 'Новая запись добавлена';	// New in N-0.0.2
$L['alreadyupdatednewentry'] = 'Запись обновлена';	// New in N-0.0.2
$L['alreadydeletednewentry'] = 'Запись удалена';	// New in N-0.0.2

/**
 * Other Section
 * Hits Subsection
 */

$L['adm_byyear'] = 'По годам';
$L['adm_bymonth'] = 'По месяцам';
$L['adm_byweek'] = 'По неделям';

$L['adm_ref_lowhits'] = 'Удалить записи с количеством хитов менее 5';
$L['adm_maxhits'] = 'Максимальное количество хитов (%2$s) зафиксировано %1$s';

/**
 * Other Section
 * Referers Subsection
 */

$L['adm_ref_prune'] = 'Очищено';
$L['adm_ref_prunelowhits'] = 'Рефералы с количеством посетителей менее 5 удалены';

/**
 * Other Section
 * Log Subsection
 */

$L['adm_log'] = 'Системный протокол';
$L['adm_infos'] = 'Информация';
$L['adm_versiondclocks'] = 'Версии и таймеры';
$L['adm_checkcoreskins'] = 'Проверить файлы ядра и скинов';
$L['adm_checkcorenow'] = 'Проверить файлы ядра!';
$L['adm_checkingcore'] = 'Проверяю файлы ядра...';
$L['adm_checkskins'] = 'Проверить наличие всех файлов в скине';
$L['adm_checkskin'] = 'Проверить TPL-файлы скина';
$L['adm_checkingskin'] = 'Проверяю скин...';
$L['adm_hits'] = 'Хиты';
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
$L['adm_queue_unvalidated'] = 'Публикация поставлена в очередь';	// New in N-0.0.3
$L['adm_queue_validated'] = 'Публикация утверждена';	// New in N-0.0.3
$L['adm_required'] = '(обязательно)';
$L['adm_setby'] = 'Установлено';
$L['adm_to'] = 'Кому';
$L['adm_totalsize'] = 'Общий объем';
$L['adm_warnings'] = 'Предупреждения';

$L['editdeleteentries'] = 'Правка / удаление';
$L['viewdeleteentries'] = 'Просмотр / удаление';

/**
 * Extra Fields (Common Entries for Pages & Users)
 */

$L['adm_extrafields'] = 'Дополнительные поля';
$L['adm_extrafield_added'] = 'Новое поле добавлено';
$L['adm_extrafield_not_added'] = 'Ошибка! Новое поле не добавлено';
$L['adm_extrafield_updated'] = 'Поле отредактировано';
$L['adm_extrafield_not_updated'] = 'Ошибка! Поле не отредактировано';
$L['adm_extrafield_removed'] = 'Поле удалено';
$L['adm_extrafield_not_removed'] = 'Ошибка! Поле не удалено';
$L['adm_extrafield_confirmdel'] = 'Вы действительно хотите удалить поле? Все данные этого поля будут потеряны!';
$L['adm_extrafield_confirmupd'] = 'Вы действительно хотите редактировать поле? Некоторые данные этого поля могут быть потеряны.';

$L['extf_Name'] = 'Название поля';
$L['extf_Type'] = 'Тип поля';
$L['extf_Base_HTML'] = 'HTML-код поля';
$L['extf_Page_tags'] = 'Тэги';
$L['extf_Description'] = 'Описание поля (_TITLE)';

$L['adm_extrafield_new'] = 'Новое поле';
$L['adm_extrafield_noalter'] = 'Не добавлять новое поле в БД, только зарегистрировать как дополнительное';
$L['adm_extrafield_selectable_values'] = 'Значения для select (через запятую)';
$L['adm_help_extrafield'] = 'HTML-код поля устанавливается в значение по умолчанию автоматически, если его очистить и обновить';

/**
 * Help messages that still don't work
 */

$L['adm_help_cache'] = 'Недоступно';
$L['adm_help_check1'] = 'Недоступно';
$L['adm_help_check2'] = 'Недоступно';
$L['adm_help_config']= 'Недоступно';
$L['adm_help_forums'] = 'Недоступно';
$L['adm_help_forums_structure'] = 'Недоступно';
$L['adm_help_pfsfiles'] = 'Недоступно';
$L['adm_help_pfsthumbs'] = 'Недоступно';

?>