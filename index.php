<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List actualizble mediante botón</title>
</head>
<body>
    <label for="content">Nueva tarea</label>
    <input type="text" id="content" placeholder="Ingresa una tarea"><br>
    <button id="guardar">Guardar</button>
    <?php
        require "DB.php";
        require "todo.php";
        try {
                $db = new DB;
                echo "<h2>TODO</h2>";
                echo "<ul id=\"lista\">";
                $todo_list = Todo::DB_selectAll($db->connection);
                foreach ($todo_list as $row) {
                    echo "<li>". $row->getItem_id() .". ". $row->getContent() . " <button onclick='borrar(".$row->getItem_id().")' >X</button></li>";
                }
                echo "</ul>";
            } 
        catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
    ?>
    <script>
        function llamada_a_controller(metodo,postData){
            const url = 'http://todo.cierva/controller.php'; 
            // Limpiar la tabla antes de agregar los nuevos datos
            const lista = document.getElementById('lista');
            lista.innerHTML = ''; // Eliminar el contenido previo de la tabla
            // La respuesta es un array de objetos JSON
            fetch(url, {
                method: metodo,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())  // Convertir la respuesta a JSON
            .then(data => {
            data.forEach(item => {
                    var li = document.createElement("li");
                    li.appendChild(document.createTextNode(item.item_id+". "+item.content+" "));
                    var button = document.createElement("button");
                    button.textContent ="X";
                    button.setAttribute("onclick","borrar("+item.item_id+")");
                    li.appendChild(button);
                    lista.appendChild(li);
                });
            })
            .catch(error => console.error('Error en la solicitud POST:', error));
        }

        function borrar(item_id) {
            const postData = {
                item_id: item_id
            };
            llamada_a_controller("DELETE",postData);
        }
            
        document.getElementById('guardar').addEventListener('click', function () {
            const contenido = document.getElementById('content').value;
            if (!contenido) {
                alert('Por favor, introduce un valor.');
                return;
            } 
            
            const postData = {
                content: contenido
            };
            llamada_a_controller("POST",postData);
            });

        
    </script>

</body>
</html>