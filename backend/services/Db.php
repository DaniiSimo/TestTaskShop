<?php
namespace Service;
use PDO;
use PDOException;

/**
 * База данных
 *
 * Данный класс, предназначен для взаимодействия с базой данных
 */
class Db
{
    /**
     * @var string Название базы данных
     */
    private readonly string $name;
    /**
     * @var string Имя источника данных
     */
    private readonly string $dsn;
    /**
     * @var string Имя пользователя для подключения
     */
    private readonly string $username;
    /**
     * @var string Пароль пользователя для подключения
     */
    private readonly string $password;
    /**
     * @var string Соединение
     */
    private readonly PDO $connection;
    /**
     * Конструктор класса
     *
     * @param string $name Название базы данных
     * @param string $host Хост для подключения
     * @param string $user Имя пользователя для подключения
     * @param string $password Пароль пользователя для подключения
     */
    public function __construct(string $name, string $host, string $user, string $password){
        $this->name = $name;
        $this->connection = new PDO(dsn: "mysql:host=".$host, username: $user, password: $password);
        $this->dsn = "mysql:host=".$host;
        $this->username = $user;
        $this->password = $password;
    }
    /**
     * Создание базы данных
     *
     * @return bool Возвращает результат создания базы данных
     */
    public function create():bool{
        $query = "CREATE DATABASE IF NOT EXISTS :name";
        $state = $this->connection->prepare(query: $query);
        return $state->execute(params: [
            "name" => $this->name
        ]);
    }
    /**
     * Удаление базы данных
     *
     * @return bool Возвращает результат удаления базы данных
     */
    public function delete():bool{
        $query = "DROP DATABASE IF NOT EXISTS :name";
        $state = $this->connection->prepare(query: $query);
        return $state->execute(params: [
            "name" => $this->name
        ]);
    }
    /**
     * Создание соединения с базой данных
     *
     * @return PDO|PDOException Возвращает соединение с базой данных или ошибку
     */
    public function createConnection():PDO|PDOException{
        try {
            return new PDO(dsn: $this->dsn.";dbname=$this->name", username: $this->username, password: $this->password);
        }
        catch (PDOException $ex){
            return $ex;
        }
    }
    /**
     * Проверка существования базы данных
     *
     * @return bool Если, база данных существует, возвращает true, иначе false
     */
    public function checkExists():bool{
        $query = "SELECT COUNT(*) FROM SCHEMATA WHERE SCHEMA_NAME = :database";
        $state = $this->connection->prepare(query: $query);
        $state->execute(params: [
            "database" => $this->name,
        ]);
        return $state->fetchColumn() != 0;
    }
    /**
     * Получение значения, поля названия базы данных
     *
     * @return string название базы данных
     */
    public function getName():string{
        return $this->name;
    }

}