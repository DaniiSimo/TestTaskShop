<?php
namespace Model;

use PDO;
use PDOException;

require_once('Table.php');
/**
 * Таблица товар
 *
 * Данный класс описывает сущность товар, который имеет, следующие поля:
 * id int - Идентификатор товара (Первичный ключ)
 * name varchar - Название товара
 * description text - Описание товара
 * price float - Цена товара
 * path_image - Путь до изображения товара
 */
class Product extends Table
{
    /**
     * Конструктор класса
     *
     * @param PDO $connection Соединение с базой данных
     */
    public function __construct(PDO $connection)
    {
        parent::__construct(connection: $connection,name: "Product");
    }
    /**
     * Создание таблицы товара в базе данных
     *
     * @return bool Возвращает результат создания таблицы товаров
     */
    public function create(): bool
    {
        $query = "CREATE TABLE IF NOT EXISTS Product (".
            "id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
            "name VARCHAR(100) NOT NULL,".
            "description text NOT NULL,".
            "price FLOAT NOT NULL,".
            "path_image VARCHAR(1000) NOT NULL,".
            "PRIMARY KEY (id))".
            "ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
        return $this->connection->exec(statement: $query);
    }
    /**
     * Добавление товара в базу данных
     *
     * @param array $params Значения столбцов, описывающих товар, должен иметь следующий вид:
     * [
     *  "name" => {Значение названия товара} string
     *  "description" => {Значение описания товара} string
     *  "price" => {Значение цены товара} float
     *  "path_image" => {Значение пути до изображения товара} string
     * ]
     * @return bool|PDOException Если, товар успешно добавлен, возвращает true, иначе ошибку
     */
    public function addRecord(array $params): bool|PDOException
    {
        $query = "INSERT INTO $this->name SET name =:name, description=:description, price=:price, path_image=:path_image";
        $state = $this->connection->prepare(query: $query);
        try {
            $state->bindValue(param: "name",value: $params["name"],type: PDO::PARAM_STR);
            $state->bindValue(param: "description",value: $params["description"],type: PDO::PARAM_STR);
            $state->bindValue(param: "price",value: $params["price"],type: PDO::PARAM_STR);
            $state->bindValue(param: "path_image",value: $params["path_image"],type: PDO::PARAM_STR);
            $state->execute();
            return true;
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
    /**
     * Редактирование товара из базы данных
     *
     * @param int $id Идентификатор товара
     * @param array $params Значения, редактируемых столбцов, описывающих товар, должны иметь следующий вид:
     * [
     *  "name" => {Значение названия товара} string
     *  "description" => {Значение описания товара} string
     *  "price" => {Значение цены товара} float
     *  "path_image" => {Значение пути до изображения товара} string
     * ]
     * @return bool|PDOException Если, товар успешно редактирован, возвращает true, иначе ошибку
     */
    public function editRecord(int $id, array $params): bool|PDOException
    {
        $query = "UPDATE $this->name SET name = :name, description = :description, price = :price, path_image = :path_image WHERE `id` = :id";
        $state = $this->connection->prepare(query: $query);
        try {
            $state->bindValue(param: "name",value: $params["name"],type: PDO::PARAM_STR);
            $state->bindValue(param: "description",value: $params["description"],type: PDO::PARAM_STR);
            $state->bindValue(param: "price",value: $params["price"],type: PDO::PARAM_STR);
            $state->bindValue(param: "path_image",value: $params["path_image"],type: PDO::PARAM_STR);
            $state->bindValue(param: "id",value: $id,type: PDO::PARAM_INT);
            $state->execute();
            return true;
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
    /**
     * Удаление товара из базы данных
     *
     * @param int $id Идентификатор, удаляемого товара
     * @return bool|PDOException Если, товар успешно удалён, возвращает true, иначе ошибку
     */
    public function deleteRecord(int $id): bool|PDOException
    {
        $query = "DELETE FROM $this->name WHERE id=:id";
        $state = $this->connection->prepare(query: $query);
        try {
            $state->bindValue(param: "id",value: $id,type: PDO::PARAM_INT);
            $state->execute();
            return true;
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
    /**
     * Получение всех товаров из базы данных
     *
     * @return array|PDOException Если, получение произошло успешно, возвращает массив всех товаров, иначе ошибку
     */
    public function getAllRecords(): array|PDOException
    {
        $query = "SELECT * FROM $this->name";
        try {
            return $this->connection->query($query)->fetchAll(mode: PDO::FETCH_NAMED);
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
    /**
     * Получение товара по идентификатору из базы данных
     *
     * @param int $id Идентификатор, получаемого товара
     * @return array|PDOException Если, получение произошло успешно, возвращает товар из базы данных, иначе ошибку
     */
    public function getRecord(int $id): array|PDOException
    {
        $query = "SELECT * FROM $this->name WHERE id=$id";
        try {
            return $this->connection->query($query)->fetch(mode: PDO::FETCH_NAMED);
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
}