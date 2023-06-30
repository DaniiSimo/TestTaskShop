<?php
namespace Model;
use PDO;
use PDOException;

/**
 * Таблица базы данных
 *
 * Данный класс является базовым, для взаимодействия с таблицами базы данных
 */
abstract class Table
{
    /**
     * @var string Название таблицы
     */
    protected readonly string $name;
    /**
     * @var PDO Соединение с базой данных
     */
    protected readonly PDO $connection;
    /**
     * Конструктор класса
     *
     * @param PDO $connection соединение с базой данных
     * @param string $name название таблицы
     */
    protected function __construct(PDO $connection,string $name){
        $this->connection = $connection;
        $this->name = $name;
    }
    /**
     * Создание таблицы в базе данных
     *
     * @return bool Возвращает результат создания таблицы
     */
    abstract public function create():bool;
    /**
     * Добавление записи в таблицу
     *
     * @param array $params Ассоциативный массив со значениями столбцов таблицы
     * @return bool|PDOException Если, запись успешно добавлена, возвращает true, иначе ошибку
     */
    abstract public function addRecord(array $params):bool|PDOException;
    /**
     * Редактирование записи таблицы
     *
     * @param int $id Идентификатор, редактируемой записи
     * @param array $params Ассоциативный массив со значениями столбцов таблицы
     * @return bool|PDOException Если, запись успешно добавлена, возвращает true, иначе ошибку
     */
    abstract public function editRecord(int $id,array $params ):bool|PDOException;
    /**
     * Удаление записи таблицы
     *
     * @param int $id Идентификатор, удаляемой записи
     * @return bool|PDOException Если, запись успешно добавлена, возвращает true, иначе ошибку
     */
    abstract public function deleteRecord(int $id):bool|PDOException;
    /**
     * Получение всех записей таблицы
     *
     * @return array|PDOException Если, получение произошло успешно, возвращает массив всех записей таблицы, иначе ошибку
     */
    abstract public function getAllRecords():array|PDOException;
    /**
     * Получение записи из таблицы
     *
     * @param int $id Идентификатор, получаемой записи
     * @return array|PDOException Если, получение произошло успешно, возвращает запись таблицы, иначе ошибку
     */
    abstract public function getRecord(int $id):array|PDOException;
    /**
     * Проверка существования таблицы в базе данных
     *
     * @return bool Если, таблица существует, возвращает true, иначе false
     */
    public function checkExists():bool{

        $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :database AND table_name = :table";
        $state = $this->connection->prepare(query: $query);
        $state->execute(params: [
            "database" => DBNAME,
            "table" => $this->name
        ]);
        return $state->fetchColumn() != 0;
    }
    /**
     * Удаление таблицы из базы данных
     *
     * @return bool Если, таблица удалилась, возвращает true, иначе false
     */
    public function delete():bool{
        return $this->connection->exec(statement: "DROP TABLE IF EXISTS $this->name");
    }
    /**
     * Получение значения, поля названия таблицы
     *
     * @return string название таблицы
     */
    public function getName():string{
        return $this->name;
    }
    /**
     * Получение значения, поля названия соединения с базой данных
     *
     * @return PDO соединение с базой данных
     */
    public function getConnection():PDO{
        return $this->connection;
    }
}