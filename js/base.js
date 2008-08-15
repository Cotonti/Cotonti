function popup(code, w, h){
    window.open('plug.php?o=' + code, '', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=' + w + ',height=' + h + ',left=32,top=16');
}

function pfs(id, c1, c2){
    window.open('pfs.php?userid=' + id + '&c1=' + c1 + '&c2=' + c2, 'PFS', 'status=1, toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=754,height=512,left=32,top=16');
}

function help(rcode, c1, c2){
    window.open('plug.php?h=' + rcode + '&c1=' + c1 + '&c2=' + c2, 'Help', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=32,top=16');
}

function comments(rcode){
    window.open('comments.php?id=' + rcode, 'Comments', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=16,top=16');
}

function ratings(rcode){
    window.open('ratings.php?id=' + rcode, 'Ratings', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=16,top=16');
}

function polls(rcode){
    window.open('polls.php?id=' + rcode, 'Polls', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=608,height=448,left=16,top=16');
}

function pollvote(rcode, rvote){
    window.open('polls.php?a=send&id=' + rcode + '&vote=' + rvote, 'Polls', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=608,height=448,left=16,top=16');
}

function picture(url, sx, sy){
    window.open('pfs.php?m=view&v=' + url, 'Picture', 'toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=' + sx + ',height=' + sy + ',left=0,top=0');
}

function redirect(url){
    location.href = url.options[url.selectedIndex].value;
}

function toggleblock(id){
    var bl = document.getElementById(id);
    if (bl.style.display == 'none') {
        bl.style.display = '';
    }
    else {
        bl.style.display = 'none';
    }
}

window.name = 'main';
