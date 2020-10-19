<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Josh's IFB102 Assignment</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <style>
    * {
      font-family: 'Quicksand', sans-serif;
    }

    .container {
      text-align: center;
    }

    .content-table {
      border-collapse: collapse;
      font-size: 0.9em;
      min-width: 400px;
      border-radius: 5px 5px 0 0;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
      width: 30rem;
      margin: 1rem auto;
    }

    .content-table thead tr {
      background-color: var(--teal);
      color: white;
      text-align: left;
      font-weight: bold;
    }

    .content-table th,
    .content-table td {
      padding: 12px 15px;
    }

    .content-table tbody tr {
      border-bottom: 1px solid #dddddd;
    }

    .content-table tbody tr:nth-of-type(even) {
      background-color: #f3f3f3;
    }

    .content-table tbody tr:last-of-type {
      border-bottom: 2px solid #009879;
    }


    .btn-primary {
      background-color: #009879
    }
  </style>
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const page = parseInt(urlParams.get('page') ?? '1');

    function nextPage() {
      location.href = `/?page=${page + 1}`

    }

    function prevPage() {
      const prevPage = Math.max(1, page - 1)
      location.href = `/?page=${prevPage}`
    }
  </script>
</head>

<body>
  <?php
    $TABLE_SIZE = 20;

    $db = new \Sqlite3("db/db.sqlite");
    $queries = array();
    parse_str($_SERVER['QUERY_STRING'], $queries);

    $page = $queries['page'] ?: 1;
    $offset = ($page - 1)  * $TABLE_SIZE;

    $output = exec('./scripts/toggle_light.py > /dev/null 2>/dev/null &');
  ?>
  <div class="container">
    <h1>Temperature and Humdity of my House</h1>
    <button class='btn btn-outline-dark' onClick="prevPage()">< Prev</button> 
    <?php
        $pageCount = ceil($db->querySingle("SELECT COUNT(timestamp) FROM ambiance") / $TABLE_SIZE);
        echo "$page / $pageCount";
        ?> 
    <button class='btn btn-outline-dark' onClick="nextPage()"> Next ></button>


    <table class="content-table">
      <thead>
        <tr>
          <th>Time</th>
          <th>Temperature °C</th>
          <th>Humidity</th>
        </tr>
      </thead>
      <?php
      $stmt = $db->query("SELECT timestamp, temperature, humidity FROM ambiance ORDER BY timestamp DESC LIMIT {$offset}, {$TABLE_SIZE}");
      while ($row = $stmt->fetchArray()) {
        echo '<tr>';
        echo '<td>' . $row['timestamp'] . '</td>';
        echo '<td>' . $row['temperature'] . '</td>';
        echo '<td>' . $row['humidity'] . '%</td>';
        echo '</tr>';
      }
      ?>
    </table>
  </div>
</body>
</html>
