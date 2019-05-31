<html>
 <head>
 <Title>Registration Form</Title>
 <style type="text/css">
 	body { background-color: #fff; border-top: solid 10px #000;
 	    color: #333; font-size: .85em; margin: 20; padding: 20;
 	    font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
 	}
 	h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
 	h1 { font-size: 2em; }
 	h2 { font-size: 1.75em; }
 	h3 { font-size: 1.2em; }
 	table { margin-top: 0.75em; }
 	th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
 	td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
 </style>
 </head>
 <body>
 <h1>Daftar di sini!</h1>
 <p>Masukkan nama dan email di sini, lalu tekan <strong>Daftar</strong> untuk mendaftar.</p>
 <form method="post" action="index.php" enctype="multipart/form-data" >
  <table>
   <tr>
       <td align='right'>Nama</td><td>:</td><td><input type="text" name="name" id="name"/></td>
   </tr>
   <tr>
       <td align='right'>Email/td><td>:</td><td><input type="text" name="email" id="email"/></td>
   </tr>
   <tr>
       <td align='right'>Pekerjaan/td><td>:</td><td><input type="text" name="job" id="job"/></td>
   </tr>
   <tr>
      <td>
        <input type="submit" name="submit" value="Daftar" />
        <input type="submit" name="load_data" value="Tampilkan data" />
      </td>
   </tr>
   </table>
 </form>
 <?php
    $host = "dicodinghostwin.database.windows.net";
    $user = "syamsu";
    $pass = "H234k123m";
    $db = "DBDicoding";

    try {
        $conn = new PDO("sqlsrv:server = $host; Database = $db", $user, $pass);
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    } catch(Exception $e) {
        echo "Failed: " . $e;
    }

    if (isset($_POST['submit'])) {
        try {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $job = $_POST['job'];
            $date = date("Y-m-d");
            // Insert data
            $sql_insert = "INSERT INTO Registration (name, email, job, date) 
                        VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $email);
            $stmt->bindValue(3, $job);
            $stmt->bindValue(4, $date);
            $stmt->execute();
        } catch(Exception $e) {
            echo "Gagal: " . $e;
        }

        echo "<h3>Selamat, kamu terdaftar!</h3>";
    } else if (isset($_POST['load_data'])) {
        try {
            $sql_select = "SELECT * FROM Registration";
            $stmt = $conn->query($sql_select);
            $registrants = $stmt->fetchAll(); 
            if(count($registrants) > 0) {
                echo "<h2>Data yang sudah terdaftar:</h2>";
                echo "<table>";
                echo "<tr><th>Nama</th>";
                echo "<th>Email</th>";
                echo "<th>Perkerjaan</th>";
                echo "<th>Tanggal</th></tr>";
                foreach($registrants as $registrant) {
                    echo "<tr><td>".$registrant['name']."</td>";
                    echo "<td>".$registrant['email']."</td>";
                    echo "<td>".$registrant['job']."</td>";
                    echo "<td>".$registrant['date']."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<h3>Belum ada yang mendaftar.</h3>";
            }
        } catch(Exception $e) {
            echo "Gagal: " . $e;
        }
    }
 ?>
 </body>
 </html>
