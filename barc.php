<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php include 'php_static/web/link-rels.php'?>
        <?php include 'php_static/web/scripts-rels.php'?>
    </head>
    <body class="sidebar-mini layout-fixed">
        <input style="width:900px" id="barcode" onkeyup="debounce(split, 150)">

        <h4>First</h4>
        <p id="first"></p>
        <h4>Second</h4>
        <p id="second"></p>
        <h4>Third</h4>
        <p id="third"></p>
    </body>
</html>


<script>
    function debounce(method, delay) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function() {
            method();
        }, delay);
    }
    function split() {
        var string = document.getElementById('barcode').value;
        console.log(string);

        $.ajax({
            url: 'barc_script.php',
            type: 'GET',
            data: {
                'string': string,
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                document.getElementById('barcode').value = '';
                
                document.getElementById('first').innerHTML = response.first;
                document.getElementById('second').innerHTML = response.second;
                document.getElementById('third').innerHTML = response.third;

            }
        });
    }
</script>