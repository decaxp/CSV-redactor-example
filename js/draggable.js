/**
 * Created by Dmitry on 26.04.2017.
 */
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("id", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var dropID = ev.dataTransfer.getData("id");
    var dropParent=$('#'+dropID.toString()).parent().parent();
    var targetParent=$('#'+ev.target.id.toString()).parent().parent();

    var dropArr=dropParent.find('input');
    var targetArr=targetParent.find('input');
    var count=dropArr.length;
    var param='';

    for(var i=0;i<count;i++){
        dropID=dropArr[i].id;
        targetID=targetArr[i].id;
        param=$('#'+targetID).val();

        $('#'+targetID).val($('#'+dropID).val());
        $('#'+dropID).val($('#'+targetID).val());

        $('#'+dropID).val(param);
    }

}