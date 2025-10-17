$(document).ready(function () {
    $('#voiceInput').click(function(){
        $(this).attr("placeholder","Search");     
    });   
    $('#voiceRecord').click(function(){ 
        var getText = $(".testVoice span").text();
        var recognition = new webkitSpeechRecognition();
        recognition.lang = "en-GB";

        recognition.onresult = function(event) {
        alert('hello');

            console.log(event);
            document.getElementById('voiceInput').value = event.results[0][0].transcript;
            var getVoise = event.results[0][0].transcript;
            // alert(getText);
            if(getVoise == getText){
                $(".testVoice span").show();
            }
        }
        navigator.mediaDevices.getUserMedia({ audio: true }).then(function(stream) {
            $('.connectedGlow').css('display', 'block')
            $('#voiceRecord').attr("src","images/whiteMasterMic.png");
            $('#voiceRecord').css('background', 'red')
            setTimeout(function(){
                $('.connectedGlow').css('display', 'none')
                $('#voiceRecord').attr("src","images/blackMasterMic.png");
                $('#voiceRecord').css('background', 'white')
            }, 8000);
        }).catch(function(err) {
            $('.connectedGlow').css('display', 'none')
            $('#voiceRecord').attr("src","images/blackMasterMic.png");
            $('#voiceRecord').css('background', 'white')
        });
        recognition.start();
        
    });  

});
                                 