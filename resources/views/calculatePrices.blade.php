<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript">
        function ShowHideDiv1() {
            var type_of_calculation1 = document.getElementById("type_of_calculation1");
            var showBox1 = document.getElementById("showBox1");
            showBox1.style.display = type_of_calculation1.checked ? "block" : "none";
            document.getElementById("type_of_calculation2").disabled = true;
            document.getElementById("type_of_calculation3").disabled = true;
        }
        function ShowHideDiv2() {
            var type_of_calculation2 = document.getElementById("type_of_calculation2");
            var showBox2 = document.getElementById("showBox2");
            showBox2.style.display = type_of_calculation2.checked ? "block" : "none";
            document.getElementById("type_of_calculation1").disabled = true;
            document.getElementById("type_of_calculation3").disabled = true;
        }
        function ShowHideDiv3() {
            var type_of_calculation3 = document.getElementById("type_of_calculation3");
            var showBox3 = document.getElementById("showBox3");
            showBox3.style.display = type_of_calculation3.checked ? "block" : "none";
            document.getElementById("type_of_calculation1").disabled = true;
            document.getElementById("type_of_calculation2").disabled = true;
        }
    </script>
</head>
<body>
<form action='{{url('test2')}}' enctype="multipart/form-data" method="post">
    {{csrf_field()}}

    <p>Please fill this form in order to calculate prices:</p>
    <div>
        <input type="radio" id="standard1"
               name="standard" value="Normal">
        <label for="Normal">Normal</label>
        <input type="radio" id="standard2"
               name="standard" value="Middle">
        <label for="Middle">Middle</label>
        <input type="radio" id="standard3"
               name="standard" value="High">
        <label for="High">High</label>
    </div>
    <div>
        <label>City/State? </label>
        <select id="cities" name="city_id">
            <?php
            $states = DB::connection('mysql_admin')->table('states')->where('country_id', 150)->get();
            foreach ($states as $s) {
            $cities = DB::connection('mysql_admin')->table('cities')->where('state_id', $s->id)->get();
            ?>
            @foreach ($cities as $bc)
                <option value="{{$bc->id}}">{{$bc->name}}</option>

            @endforeach

            <?php
            }
            ?>
        </select>
    </div>
    <div>
        <input type="radio" id="type_of_calculation1"
               name="type_of_calculation" value="Ten square feet" onclick="ShowHideDiv1()">
        <label for="Ten_sqft">Ten square feet</label>
        <input type="radio" id="type_of_calculation2"
               name="type_of_calculation" value="One square feet" onclick="ShowHideDiv2()">
        <label for="One_sqft">One square feet</label>
        <input type="button" id="type_of_calculation3"
               name="type_of_calculation" value="Custom" onclick="ShowHideDiv3()" >
        <label for="Custom">Custom</label>
    </div>
    <div id="showBox1" style="display: none">
        Numbers of ten square feet :
        <input type="text" id="txtShowBox1" name="txtShowBox1">
    </div>
    <div id="showBox2" style="display: none">
        Numbers of one square feet :
        <input type="text" id="txtShowBox2" name="txtShowBox2">
    </div>
    <div id="showBox3" style="display: none">
        Numbers of custom square feet :
        <input type="text" id="txtShowBox3" name="custom" >
    </div>

    <div>
        <button type="submit">Calculate</button>
    </div>

</form>
</body>
</html>