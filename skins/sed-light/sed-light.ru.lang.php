<?PHP
/**
 * Russian Language File for Sed-Light Skin
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * comments.tpl
 */

$skinlang['comments']['Comment'] = 'Ваш комментарий';
$skinlang['comments']['Postedby'] = 'Разместил';

/**
 * forums.newtopic.tpl
 */

$skinlang['forumsnewtopic']['privatetopic1'] = 'Создать &laquo;частную&raquo; тему';
$skinlang['forumsnewtopic']['privatetopic2'] = 'Просмотр и ответы в теме будут доступны только модераторам форумов и вам как автору темы';

/**
 * forums.posts.tpl
 */

$skinlang['forumspost']['privatetopic'] = 'Это частная тема: доступ к просмотру и ответам только для модераторов и автора темы.';
$skinlang['forumspost']['Onlinestatus0'] = 'не в сети'; // N-0.0.2
$skinlang['forumspost']['Onlinestatus1'] = 'в сети'; // N-0.0

/**
 * forums.sections.tpl
 */

$skinlang['forumssections']['Searchinforums'] = 'Поиск в форумах';
$skinlang['forumssections']['Markasread'] = 'Отметить все как прочитанные';
$skinlang['forumssections']['Activity'] = 'Активность';
$skinlang['forumssections']['FoldAll'] = 'Свернуть все';
$skinlang['forumssections']['UnfoldAll'] = 'Развернуть все';

/**
 * forums.topics.tpl
 */

$skinlang['forumstopics']['Newtopic'] = 'Новая тема'; // New in v125
$skinlang['forumstopics']['Nonewposts'] = 'Нет новых сообщений';
$skinlang['forumstopics']['Newposts'] = 'Есть новые сообщения';
$skinlang['forumstopics']['Nonewpostspopular'] = 'Популярная (нет новых сообщений)';
$skinlang['forumstopics']['Newpostspopular'] = 'Популярная (есть новые сообщения)';
$skinlang['forumstopics']['Sticky'] = 'Тема закреплена (нет новых сообщений)';
$skinlang['forumstopics']['Newpostssticky'] = 'Тема закреплена (есть новые сообщения)';
$skinlang['forumstopics']['Locked'] = 'Тема закрыта (нет новых сообщений)';
$skinlang['forumstopics']['Newpostslocked'] = 'Тема закрыта (есть новые сообщения)';
$skinlang['forumstopics']['Announcment'] = 'Обьявление';
$skinlang['forumstopics']['Newannouncment'] = 'Новые обьявления';
$skinlang['forumstopics']['Movedoutofthissection'] = 'Перенесена в другой раздел';
$skinlang['forumstopics']['Viewers'] = 'Просматривают';

/**
 * header.tpl
 */

$skinlang['header']['Lostyourpassword'] = 'Восстановить пароль';
$skinlang['header']['Welcome'] = 'Добро пожаловать!';

/**
 * index.tpl
 */

$skinlang['index']['Newinforums'] = 'Новое на форумах';
$skinlang['index']['Recentadditions'] = 'Новое в разделах';
$skinlang['index']['Online'] = 'Онлайн';

/**
 * list.tpl
 */

$skinlang['list']['linesperpage'] = 'записей на страницу';
$skinlang['list']['linesinthissection'] = 'записей в разделе';
$skinlang['list']['hits'] = 'просмотров';

/**
 * page.tpl
 */

$skinlang['page']['Submittedby'] = 'Опубликовал';
$skinlang['page']['Summary'] = 'Содержание';
$skinlang['page']['Filesize'] = 'Размер файла';
$skinlang['page']['downloaded'] = 'скачан';
$skinlang['page']['times'] = 'раз';
$skinlang['page']['Membersrating'] = 'Рейтинг';	// Out?

/**
 * page.add.tpl
 */

$skinlang['pageadd']['File'] = 'Прикрепить файл';
$skinlang['pageadd']['Filehint'] = '(при включении модуля загрузок заполните два поля ниже)';
$skinlang['pageadd']['URLhint'] = '(если прикреплен файл)';
$skinlang['pageadd']['Filesize'] = 'Размер файла (Кб)';
$skinlang['pageadd']['Filesizehint'] = '(если прикреплен файл)';
$skinlang['pageadd']['Formhint'] = 'После заполнения формы страница будет помещена в очередь на утверждение и будет скрыта до тех пор, пока модератор или администратор не утвердят ее публикацию в соответствующем разделе. Внимательно проверьте правильность заполнения полей формы &mdash; вы не сможете отредактировать их после публикации.<br />Если вам все-таки понадобится изменить содержание страницы, обратитесь к модератору или администратору.';

/**
 * page.edit.tpl
 */

$skinlang['pageedit']['File'] = 'Прикрепить файл';
$skinlang['pageedit']['Filehint'] = '(при включении модуля загрузок заполните два поля ниже)';
$skinlang['pageedit']['URLhint'] = '(если прикреплен файл)';
$skinlang['pageedit']['Filesize'] = 'Размер файла (Кб)';
$skinlang['pageedit']['Filesizehint'] = '(если прикреплен файл)';
$skinlang['pageedit']['Filehitcount'] = 'Загрузок';
$skinlang['pageedit']['Filehitcounthint'] = '(если прикреплен файл)';
$skinlang['pageedit']['Pageid'] = 'ID страницы';
$skinlang['pageedit']['Deletethispage'] = '!Удалить страницу!';

/**
 * pfs.tpl
 */

$skinlang['pfs']['Insertasthumbnail'] = 'Вставить миниатюру';
$skinlang['pfs']['Insertasimage'] = 'Вставить полноразмерное изображение';
$skinlang['pfs']['Insertaslink'] = 'Вставить в виде ссылки на файл';
$skinlang['pfs']['Dimensions'] = 'Размеры';

/**
 * pm.send.tpl
 */

$skinlang['pmsend']['Sendmessageto'] = 'Получатель';
$skinlang['pmsend']['Sendmessagetohint'] = '(до 10 адресатов, через запятую)';
$skinlang['pmsend']['Subject'] = 'Тема';

/**
 * pm.tpl
 */

$skinlang['pm']['Sender'] = 'Отправитель';
$skinlang['pm']['Subject'] = 'Тема (подробно)';
$skinlang['pm']['Recipient'] = 'Получатель';
$skinlang['pm']['Subject'] = 'Тема';
$skinlang['pm']['Sender'] = 'Отправитель';
$skinlang['pm']['Recipient'] = 'Получатель';
$skinlang['pm']['Newmessage'] = 'Новое сообщение';
$skinlang['pm']['Sendtoarchives'] = 'Переместить в архив';

/**
 * polls.tpl
 */

$skinlang['polls']['voterssince'] = 'проголосовавших с';
$skinlang['polls']['Allpolls'] = 'Все голосования';

/**
 * ratings.tpl
 */

$skinlang['ratings']['Averagemembersrating'] = 'Пользовательская оценка (от 1 до 10)';	// Problem
$skinlang['ratings']['Votes'] = 'Проголосовавших';	// Problem
$skinlang['ratings']['Rate'] = 'Оценка';	// Problem

/**
 * users.tpl
 */

$skinlang['users']['usersperpage'] = 'пользователей на страницу';
$skinlang['users']['usersinthissection'] = 'всего пользователей';

/**
 * users.auth.tpl
 */

$skinlang['usersauth']['Rememberme'] = 'Запомнить меня';
$skinlang['usersauth']['Lostpassword'] = 'Восстановить пароль';
$skinlang['usersauth']['Maintenance'] = 'Режим обслуживания (Maintenance Mode): вход разрешен только администраторам'; // N-0.0.2
$skinlang['usersauth']['Maintenancereason'] = 'Причина'; // N-0.0.2

/**
 * users.details.tpl
 */

$skinlang['usersdetails']['Sendprivatemessage'] = 'Отправить личное сообщение';

/**
 * users.edit.tpl
 */

$skinlang['usersedit']['UserID'] = 'ID пользователя';
$skinlang['usersedit']['Newpassword'] = 'Установить новый пароль';
$skinlang['usersedit']['Newpasswordhint'] = '(оставьте пустым чтобы сохранить текущий)';
$skinlang['usersedit']['Hidetheemail'] = 'Скрывать E-mail';
$skinlang['usersedit']['PMnotify'] = 'Уведомлять о новых личных сообщениях';
$skinlang['usersedit']['PMnotifyhint'] = '(получать E-mail уведомление при получении нового личного сообщения)';
$skinlang['usersedit']['LastIP'] = 'Последний IP';
$skinlang['usersedit']['Logcounter'] = 'Всего авторизаций';
$skinlang['usersedit']['Deletethisuser'] = '!Удалить пользователя!';

/**
 * users.profile.tpl
 */

$skinlang['usersprofile']['Emailpassword'] = 'Ваш текущий пароль';
$skinlang['usersprofile']['Emailnotes'] = 'Смена E-mail адреса (если разрешена):<ol>
											<li>Вы не можете использовать текущий E-mail.</li>
											<li>Вам необходимо указать текущий пароль в целях безопасности.</li>
											<li>Вам придется реактивировать аккаунт по электронной почте, чтобы доказать достоверность адреса.</li>
											<li>Ваш аккаунт будет заморожен до тех пор, пока вы не пройдете по ссылке валидации.</li>
											<li>После перехода по ссылке, ваш аккаунт будет активирован.</li>
											<li>Вводите свой E-mail адрес осторожно, у вас не будет возможности что-либо исправить.</li>
											<li>Если несмотря на предупреждения вы ошиблись в адресе, обратитесь к администратору.</li>
											</ol>Все это верно, если валидация адреса обязательна. В ином случае изменения вступают в силу немедленно.'; // N-0.1.0
$skinlang['usersprofile']['Hidetheemail'] = 'Скрывать E-mail';
$skinlang['usersprofile']['PMnotify'] = 'Уведомлять о новых личных сообщениях';
$skinlang['usersprofile']['PMnotifyhint'] = '(получать E-mail уведомление при получении нового личного сообщения)';
$skinlang['usersprofile']['Newpassword'] = 'Установить новый пароль';
$skinlang['usersprofile']['Newpasswordhint1'] = '(оставьте пустым чтобы сохранить текущий)';
$skinlang['usersprofile']['Newpasswordhint2'] = '(введите новый пароль дважды)'; // N-0.0.2
$skinlang['usersprofile']['Oldpasswordhint'] = '(введите свой текущий пароль чтобы установить новый)'; // N-0.0.2

/**
 * users.register.tpl
 */

$skinlang['usersregister']['Validemail'] = 'Действующий E-mail';
$skinlang['usersregister']['Validemailhint'] = '(необходим для подтверждения регистрации!)';
$skinlang['usersregister']['Confirmpassword'] = 'Подтвердить пароль';
$skinlang['usersregister']['Formhint'] = 'После успешной регистрации и входа в систему рекомендуем отредактировать свою учетную запись, создав аватар, подпись, введя номер ICQ, домашнюю страницу, город, часовой пояс, и проч.';

/**
 * pagination
 */

$L['pagenav_first'] = '&lt;&lt;';	// New in N-0.0.2
$L['pagenav_prev'] = '&lt;';	// New in N-0.0.2
$L['pagenav_next'] = '&gt;';	// New in N-0.0.2
$L['pagenav_last'] = '&gt;&gt;';	// New in N-0.0.2

?>