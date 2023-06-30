<?php
namespace Model;

use PDO;
use PDOException;

require_once('Table.php');
/**
 * Таблица корзина
 *
 * Данный класс описывает сущность корзину, в которую добавляются товары пользователем, имеет, следующие поля:
 * id int - Идентификатор записи в корзине (Первичный ключ)
 * id_product int - Идентификатор товара (Вторичный ключ Product(id))
 */
class Basket extends Table
{
    /**
     * Конструктор класса
     *
     * @param PDO $connection Соединение с базой данных
     */
    public function __construct(PDO $connection)
    {
        parent::__construct(connection: $connection,name: "Basket");
    }
    /**
     * Создание таблицы корзина в базе данных
     *
     * @return bool Возвращает результат создания таблицы корзины
     */
    public function create(): bool
    {
        $query = "CREATE TABLE IF NOT EXISTS Basket (".
            "id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,".
            "id_product INT(11) UNSIGNED NOT NULL,".
            "PRIMARY KEY (id),".
            "FOREIGN KEY (id_product) REFERENCES Product(id))".
            "ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
        return $this->connection->exec(statement: $query);
    }
    /**
     * Добавление записи в корзину из базы данных
     *
     * @param array $params Значения столбца, описывающего запись из корзины, должен иметь следующий вид:
     * [
     *  "id_product" => {Значение идентификатора товара, добавляемого в корзину} int]
     * @return bool|PDOException Если, запись корзины успешно добавлена, возвращает true, иначе ошибку
     */
    public function addRecord(array $params): bool|PDOException
    {
        $query = "INSERT INTO $this->name SET id_product = :id_product";
        $state = $this->connection->prepare(query: $query);
        try {
            $state->bindValue(param: "id_product",value: $params["id_product"],type: PDO::PARAM_INT);
            $state->execute();
            return true;
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
    /**
     * Редактирование записи в корзине из базы данных
     *
     * @param int $id Идентификатор записи корзины
     * @param array $params Значения столбца, описывающего запись из корзины, должен иметь следующий вид:
     * [
     *  "id_product" => {Редактируемое значение идентификатора товара, который находится в корзине} int]
     * @return bool|PDOException Если, запись корзины успешно редактирована, возвращает true, иначе ошибку
     */
    public function editRecord(int $id, array $params): bool|PDOException
    {
        $query = "UPDATE $this->name SET id_product = :id_product WHERE `id` = :id";
        $state = $this->connection->prepare(query: $query);
        try {
            $state->bindValue(param: "id_product",value: $params["id_product"],type: PDO::PARAM_INT);
            $state->bindValue(param: "id",value: $id,type: PDO::PARAM_INT);
            $state->execute();
            return true;
        }
        catch(PDOException $ex){
            return $ex;
        }
    }
    /**
     * Удаление записи из корзины
     *
     * @param int $id Идентификатор, удаляемой записи корзины
     * @return bool|PDOException Если, запись корзины успешно удалена, возвращает true, иначе ошибку
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
     * Получение всех записей корзины из базы данных
     *
     * @return array|PDOException Если, получение произошло успешно, возвращает массив всех записей корзины, иначе ошибку
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
     * Получение записи корзины из базы данных
     *
     * @return array|PDOException Если, получение произошло успешно, возвращает запись корзины, иначе ошибку
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