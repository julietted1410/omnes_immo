function maBoucle() {
    setTimeout(function() {
        maBoucle();
    }, 1000);
}
maBoucle();

$(document).ready(function() {
    var $img = $('#carrousel img');
    var indexImg = $img.length - 1;
    var i = 0;
    var $currentImg = $img.eq(i);

    $img.hide();
    $currentImg.show();

    function updateThumbnails() {
        $('.thumbnail').removeClass('active');
        $('#thumb' + (i + 1)).addClass('active');
    }

    updateThumbnails();

    $('#next').click(function() {
        i++;
        if (i > indexImg) i = 0;
        $img.hide();
        $img.eq(i).show();
        updateThumbnails();
    });

    $('#prev').click(function() {
        i--;
        if (i < 0) i = indexImg;
        $img.hide();
        $img.eq(i).show();
        updateThumbnails();
    });

    function slideImg() {
        setTimeout(function() {
            i = (i < indexImg) ? i + 1 : 0;
            $img.hide();
            $img.eq(i).show();
            updateThumbnails();
            slideImg();
        }, 4000);
    }

    slideImg();
});
