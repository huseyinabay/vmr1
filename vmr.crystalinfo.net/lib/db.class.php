<?php
class DB

{
    private bool $defaultDebug = false;
    private float $mtStart;
    private int $nbQueries = 0;
    private ?mysqli_result $lastResult = null;
    private mysqli $connection;
	//public $connection;
	
	public function getConnection()
{
    return $this->connection;
}

    public function __construct(string $server, string $user, string $pass, string $base)
    {
        $this->mtStart = $this->getMicroTime();
        $this->connection = mysqli_connect("127.0.0.1", $user, $pass, $base);

		if (!$this->connection) {
		throw new Exception("Database connection failed: " . mysqli_connect_error());
			}
    }

    public function query(string $query, int $debug = -1): mysqli_result|bool
{
    $this->nbQueries++;
    $result = mysqli_query($this->connection, $query);

    if ($result === false) {
        // Sorgu başarısızsa burası çalışır
        $this->debugAndDie($query);
    }

    // Debug ve benzeri çıktılar
    $this->debug($debug, $query, $result);

    // Burada artık $result true veya mysqli_result nesnesi olabilir
    return $result;
}



    private function execute($query, $debug = -1)
    {
      $this->nbQueries++;
      mysqli_query($this->connection,$query) or $this->debugAndDie($query);

      $this->debug($debug, $query);
    }
	
	private function yolla($query, $debug = -1)
    {
      $this->nbQueries++;
      mysqli_query($this->connection,$query);

          }




    /** Get the result of the query as value. The query should return a unique cell.\n
      * Note: no need to add "LIMIT 1" at the end of your query because
      * the method will add that (for optimisation purpose).
      * @param $query The query.
      * @param $debug If true, it output the query and the resulting value.
      * @return A value representing a data cell (or NULL if result is empty).
      */
   private function queryUniqueValue($query, $debug = -1)
    {
      $query = "$query LIMIT 1";

      $this->nbQueries++;
      $result = mysqli_query($this->connection,$query) or $this->debugAndDie($query);
      $line = mysqli_fetch_row($result);

      $this->debug($debug, $query, $result);

      return $line[0];
    }
    /** Get the maximum value of a column in a table, with a condition.
      * @param $column The column where to compute the maximum.
      * @param $table The table where to compute the maximum.
      * @param $where The condition before to compute the maximum.
      * @return The maximum value (or NULL if result is empty).
      */
    function maxOf($column, $table, $where)
    {
      return $this->queryUniqueValue("SELECT MAX(`$column`) FROM `$table` WHERE $where");
    }
    /** Get the maximum value of a column in a table.
      * @param $column The column where to compute the maximum.
      * @param $table The table where to compute the maximum.
      * @return The maximum value (or NULL if result is empty).
      */
    function maxOfAll($column, $table)
    {
      return $this->queryUniqueValue("SELECT MAX(`$column`) FROM `$table`");
    }
    /** Get the count of rows in a table, with a condition.
      * @param $table The table where to compute the number of rows.
      * @param $where The condition before to compute the number or rows.
      * @return The number of rows (0 or more).
      */
    function countOf($table, $where)
    {
      return $this->queryUniqueValue("SELECT COUNT(*) FROM `$table` WHERE $where");
    }
  
 


	
	private function debug($debug, $query, $result): void
{
    if ($debug === -1) {
        $debug = $this->defaultDebug;
    }

    if ($debug) {
        echo "<p>Query: " . htmlentities($query) . "</p>";
        if ($result) {
            echo "<p>Query executed successfully.</p>";
        } else {
            echo "<p>Error: " . mysqli_error($this->connection) . "</p>";
        }
    }
}


	public function queryUniqueObject($query, $debug = -1)
{
    $query = "$query LIMIT 1";

    $this->nbQueries++;
    $result = mysqli_query($this->connection, $query) or $this->debugAndDie($query);

    // Hata ayıklama çağrısını kaldırıyoruz
    return mysqli_fetch_object($result);
}

	
	public function countOfAll(string $table): int
{
    $query = "SELECT COUNT(*) AS count FROM $table";
    $result = $this->query($query);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['count'];
}
	

    public function fetchNextObject(?mysqli_result $result = null): ?object
    {
        $result = $result ?? $this->lastResult;

        if (is_null($result) || mysqli_num_rows($result) < 1) {
            return null;
        }

        return mysqli_fetch_object($result);
    }

    public function numRows(?mysqli_result $result = null): int
    {
        $result = $result ?? $this->lastResult;
        return $result ? mysqli_num_rows($result) : 0;
    }

    public function lastInsertedId(): int
    {
        return mysqli_insert_id($this->connection);
    }

    public function close(): void
    {
        mysqli_close($this->connection);
    }

    private function getMicroTime(): float
    {
        return microtime(true);
    }

    private function debugAndDie(string $query): void
    {
        $this->debugQuery($query, "Error");
        die("<p style=\"margin: 2px;\">" . mysqli_error($this->connection) . "</p></div>");
    }

    private function debugQuery(string $query, string $reason = "Debug"): void
    {
        $color = ($reason === "Error") ? "red" : "orange";
        echo "<div style=\"border: solid $color 1px; margin: 2px;\">"
            . "<p style=\"margin: 0 0 2px 0; padding: 0; background-color: #DDF;\">"
            . "<strong style=\"padding: 0 3px; background-color: $color; color: white;\">$reason:</strong> "
            . "<span style=\"font-family: monospace;\">" . htmlentities($query) . "</span></p></div>";
    }
}
?>
