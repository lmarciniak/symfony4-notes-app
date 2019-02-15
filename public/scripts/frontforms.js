$(document).ready(function () {
    let src = [
        { 'href': '/list', 'dst': '.notes' },
        { 'href': '/shared', 'dst': '.shared' }
    ];
    $.each(src, function (srcKey, srcElement) {
        $.get(srcElement.href, function (data) {
            $.each(data, function (dataKey, dataElement) {
                if (this.owner) {
                    $(srcElement.dst).append(`<li><div><h3>${this.title}</h3>${this.content}</div><h4>owner:${this.owner}</h4></li>`);
                } else {
                    $(srcElement.dst).append(`<li><div><h3>${this.title}</h3>${this.content}</div>
                        <a class='remove' href='/remove/${this.id}'><button class="btn btn-danger">remove</button></a> 
                        <a href='/share/${this.id}'><button class="btn btn-primary">share</button></a></li>`);
                }
            });
        });
    });
});

$("#shared-header").click(function () {
    $(".shared").toggleClass("hide");
});

$("#notes-header").click(function () {
    $(".notes").toggleClass("hide");
});

$(".remove").click(function () {
    const url = this.href;
    $.get(url, function (data) {
        if (data.id != 0) {
            this.parentNode.remove();
        }
    });
    alert('your note has been removed');
    return false;
});

$("form[name=note]").submit(function () {
    const url = $(this).attr('action');
    const data = $(this).serialize();
    $.post(url, data, function (o) {
        $(".notes").append(`<li><div><h3>${o.title}</h3>${o.content}</div></li><a class='remove' href='/remove/${o.id}'><button class="btn btn-danger">remove</button></a> 
                        <a href='/share/${o.id}'><button class="btn btn-primary">share</button></a>`);
    }, 'json');
    this.reset();
    alert("your note has been added!");
    const counter = parseInt($("#notesCounter").text());
    $("#notesCounter").text(counter + 1);
    return false;
});