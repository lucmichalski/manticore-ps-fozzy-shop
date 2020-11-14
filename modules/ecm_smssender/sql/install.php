<?php
$sql = array();
//Таблица сохраненных сообщений.
$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecm_smssender` (
    `id_ecm_smssender` int(11) NOT NULL AUTO_INCREMENT,
    `id_lang` int(11) NOT NULL,
    `payment_message` text NOT NULL,
    `shipping_message` text NOT NULL,
    `pwd_message` text NOT NULL,
    `recv_message` text NOT NULL,
    `alert_message` text NOT NULL,
    `closed_status_message` text NOT NULL,
    `noname1_status_message` text NOT NULL,
    `noname2_status_message` text NOT NULL,
    PRIMARY KEY  (`id_ecm_smssender`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

//Таблица вопросов опросника.
$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecm_smsquestion` (
    `id_question` int(11) NOT NULL AUTO_INCREMENT COMMENT "ID вопроса",
    `question_ru` VARCHAR(255) NOT NULL COMMENT "Вопрос(RU)",
    `question_ua` VARCHAR(255) NOT NULL COMMENT "Вопрос(UA)",
    PRIMARY KEY  (`id_question`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

//Таблица списка ответов опросника.
$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecm_smsanswer` (
    `id_answer` int(11) NOT NULL AUTO_INCREMENT COMMENT "ID ответа",
    `id_question` int(11) NOT NULL COMMENT "ID вопроса",
    `answer_ru` VARCHAR(255) NOT NULL COMMENT "Ответ(RU)",
    `answer_ua` VARCHAR(255) NOT NULL COMMENT "Ответ(UA)",
    PRIMARY KEY  (`id_answer`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

//Таблица записей опрошеных криентов.
$sql[] .= 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecm_smslistanswer` (
    `id_listanswer` int(11) NOT NULL AUTO_INCREMENT COMMENT "ID опроса",
    `id_customer` int(11) NOT NULL COMMENT "ID пользователя",
    `id_order` int(11) NOT NULL COMMENT "ID заказа",
    `id_question_ru` int(11) NOT NULL COMMENT "ID вопроса (RU)",
    `id_question_ua` int(11) NOT NULL COMMENT "ID вопроса (UA)",
    `id_answer_ru` int(11) NOT NULL COMMENT "ID ответа (RU)",
    `id_answer_ua` int(11) NOT NULL COMMENT "ID ответа (UA)",
    PRIMARY KEY  (`id_listanswer`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
$languages = Language::getLanguages();
foreach($languages as $language) {
 Db::getInstance()->insert('ecm_smssender', array('payment_message'=> 'Пример. Оплата заказа №{id_order}','id_lang'=>$language['id_lang']));
}
 Db::getInstance()->update('ecm_smssender', array('shipping_message'=> 'Пример. Заказ №{id_order} отправлен перевозчиком. Трек номер {track_shipping}', 'pwd_message' => 'Пример. Уважаемый {customer}. Спасибо за регистрацию. Ваш пароль {pwd}', 'recv_message'=> 'Пример. Уважаемый {customer}. Оплата заказа №{id_order}. Переведите деньги в сумме {total_paid} на наш счет', 'alert_message'=> 'Пример. Уважаемый {customer}. Заказ №{id_order} хранится на складе перевозчика более 3 дней. Не забудьте забрать его', 'closed_status_message' => 'Ссылка на страничку для оценки работы магазина: http://fozzyshop.com.ua/smsquestion?id_order={id_order}', 'noname1_status_message' => 'test1', 'noname2_status_message' => 'test2'));

//Записи в таблицу вопросов.
Db::getInstance()->insert('ecm_smsquestion', array('question_ru'=> 'Оцените срочность доставки.'));
Db::getInstance()->insert('ecm_smsquestion', array('question_ru'=> 'Остались ли Вы довольны работой оператора контакт-центра?'));
Db::getInstance()->insert('ecm_smsquestion', array('question_ru'=> 'Остались ли Вы довольны работой курьера?'));
Db::getInstance()->insert('ecm_smsquestion', array('question_ru'=> 'Оцените качество собранных товаров.'));

//Записи в таблицу ответов.
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '1', 'answer_ru'=> 'Заказ доставлен вовремя;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '1', 'answer_ru'=> 'Заказ доставлен с опозданием;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '1', 'answer_ru'=> 'Заказ не был доставлен;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '1', 'answer_ru'=> 'Я целиком отказался от заказа;'));

Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '2', 'answer_ru'=> 'Я очень доволен и хочу отметить работу моего оператора;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '2', 'answer_ru'=> 'Затрудняюсь ответить;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '2', 'answer_ru'=> 'Очень недоволен;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '2', 'answer_ru'=> 'Сообщить о проблеме;'));

Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '3', 'answer_ru'=> 'Я очень доволен и хочу отметить работу моего курьера;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '3', 'answer_ru'=> 'Затрудняюсь ответить;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '3', 'answer_ru'=> 'Очень недоволен;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '3', 'answer_ru'=> 'Сообщить о проблеме;'));

Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '4', 'answer_ru'=> 'Я очень доволен и хочу отметить работу моего сборщика;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '4', 'answer_ru'=> 'Удовлетворительное;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '4', 'answer_ru'=> 'Плохое;'));
Db::getInstance()->insert('ecm_smsanswer', array('id_question' => '4', 'answer_ru'=> 'Сообщить о проблеме;'));

