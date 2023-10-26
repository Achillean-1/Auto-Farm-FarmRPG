<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <title>Auto Farm FarmRPG</title>
</head>
<body>

<script>
    $(document).ready(function() {
        function autoFarm() {
            request = $.ajax({
                type: 'GET',
                url: 'farmrpg.php?action=farm',
                success: function(data) {
                    console.log(data);
                    setTimeout(autoFarm, 61000);
                }
            });
        }

        function autoFish() {
            request = $.ajax({
                type: 'GET',
                url: 'farmrpg.php?action=fish',
                success: function(data) {
                    console.log(data);
                    setTimeout(autoFish, 10000);
                }
            });
        }

        function autoExplore() {
            request = $.ajax({
                type: 'GET',
                url: 'farmrpg.php?action=explore',
                success: function(data) {
                    console.log(data);
                    setTimeout(autoExplore, 61000); 
                }
            });
        }

        autoFarm(); 
        autoFish(); 
        autoExplore(); 
    });
</script>

</body>
</html>
