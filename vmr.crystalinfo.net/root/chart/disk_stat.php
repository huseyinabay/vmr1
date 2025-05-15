<!DOCTYPE html>
<html>
<head>
<title>Disk Durumu</title>
<style type="text/css">
BODY {
    width: 400PX;
}

#chart-container {
    width: 100%;
    height: auto;
}
</style>
<script type="text/javascript" src="/scripts/jquery.min.js"></script>
<script type="text/javascript" src="/scripts/Chart.min.js"></script>


</head>
<body>
    <div id="chart-container">
        <canvas id="pie-chart" width="800" height="450"></canvas>
    </div>


	<script>
new Chart(document.getElementById("pie-chart"), {
    type: 'pie',
    data: {
      labels: ["Boş %", "Dolu %"],
      datasets: [{
        label: "Population (millions)",
        backgroundColor: ["#3cba9f","#c45850"],
        data: ["<?php echo $_GET["bos"];?>","<?php echo $_GET["dolu"];?>"]
      }]
    },
    options: {
      title: {
        display: true,
        text: 'Disk Durumu'
      }
    }
});
</script>
		
</body>
</html>