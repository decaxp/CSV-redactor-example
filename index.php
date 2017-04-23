<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSV Dolgov</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://yastatic.net/jquery/3.1.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.file-input.js"></script>
    <script src="js/cookie.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="header">
        <h3 class="text-muted">CSV Dolgov</h3>
    </div>

    <div class="jumbotron">
        <p class="lead">Онлайн - редактор CSV файлов</p>

                <input type="hidden" name="uploadFileName" id="uploadFileName">

                <form id="fileUploadForm" name="fileUploadForm" action="save.php" method="POST" enctype="multipart/form-data">
                    <input id="userfile" type="file" name="userfile" title="Обзор файлов" class="btn-primary fleft marginRight20">
                    <input type="submit" name="submit"  class="btn btn-info" value="Загрузить">
                    <div class="clear"></div>
                </form>


    </div>

    <hr>

    <table id="csvTable"  class="table table-bordered table-hover"></table>

    <hr>

        <input type="button" onClick="sendForm()" class="none btn btn-primary" value="Выгрузить">
        <input type="button" onClick="deleteForm()" class="none btn btn-danger" value="Стереть">

</div>
<script>
    $('input[type=file]').bootstrapFileInput();
    $('.file-inputs').bootstrapFileInput();
</script>
<script>
    var isUpload=false;
    var filename;

    $("#fileUploadForm").submit(function(){
        var formData = new FormData($(this)[0]);
        var url = "save.php"; // the script where you handle the form input.
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            beforeSend:function () {
//                var name=$('#userfile').val();
//                name=name.substring(name.lastIndexOf('\\')+1,name.length);
//                $('#uploadFileName').val(name);
                isUpload=false;
//                $('#csvTable tr').remove();

            },
            success: function(responseData, textStatus, jqXHR) {
                isUpload=true;

                var name=$('#userfile').val();
                name=name.substring(name.lastIndexOf('\\')+1,name.length);
                var date = new Date;
                date.setDate(date.getDate() + 30);//1 месяц
                setCookie('csvFileName',name)
                filename=name;
                alert(responseData);

                ajaxFileReceive(name);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });

    function makeTable(obj){
        var rows=Object.keys(obj).length;
        var cols=0;
        for(var key in obj){
            var len=Object.keys(obj[key]).length-1;

            if (cols<len){
                cols=len;
            }
        }
        var str="",value="";

        for(var i=0;i<rows;i++){
            str+="<tr>";
            for(var z=0;z<cols;z++){
                value=obj[i].hasOwnProperty(z)?obj[i][z]:"";
                str+="<td><input type='text' class='form-control input-md' value='"+value.toString()+"'></td>";
            }
            str+="</tr>";
        }
//        console.log(str);

        $('#csvTable').html(str);
    }

    function ajaxFileReceive(name){
        var data={'type':'get','name':name};
        $.ajax({
            url: "edit.php",
            type: 'POST',
            data: data,
//            beforeSend:function () {
//                $('#csvTable tr').remove();
//            },
            success: function(responseData, textStatus, jqXHR) {
                var obj=JSON.parse(responseData);
                makeTable(obj);
                $('.none.btn').removeClass('none');

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }

        });
    }

    function tableTOJson() {
        var arr=[],row=[];
        $('#csvTable tr').each(function(i, tr){
            tds = $(tr).find('td>input');
            row=[];
            tds.each(function(index, inp){
                someText = inp.value.replace(/(\r\n|\n|\r)/gm,"");
                row.push(someText);
            });
            arr.push(row);
        });
        return JSON.stringify(arr);

    }

    function sendForm() {
        var data={'type':'save','name':filename,'table':tableTOJson()};
        console.log(tableTOJson());
        $.ajax({
            url: "edit.php",
            type: 'POST',
            data: data,
            success: function(responseData, textStatus, jqXHR) {
                alert(responseData);

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }

        });
    }

    $(document).ready(function(){
        filename=getCookie('csvFileName');
        if (filename!=undefined){
            ajaxFileReceive(filename);
        }
    });
</script>
</body>
</html>