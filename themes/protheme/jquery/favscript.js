            categoryid = "favourite";

            var addfavstreamidsave = {};

            favchannelget = localStorage.getItem("favourite");
            newfavchannelget = JSON.parse(favchannelget);
            favarray = streamid; 
            newfavarray = [];
            if(newfavchannelget != null)
            {
                if(typeof newfavchannelget[anyname] != "undefined") 
                {
                    newfavarray = newfavchannelget[anyname];
                }
            }
            newfavarray.splice(newfavarray.indexOf(streamid), 1);

            addfavstreamidsave[anyname] = newfavarray;
            
            finalenewarray = {};
            $.each(newfavchannelget, function( index, value ) {
                finalenewarray[index] = value;
            });
        
            if(typeof finalenewarray[anyname] != "undefined") 
            {
                if(finalenewarray[anyname].length > 0)
                {
                    finalenewarray[anyname] = addfavstreamidsave[anyname];    
                } 
            }
            else
            {
                finalenewarray[anyname] = addfavstreamidsave[anyname];
            }

            localStorage.setItem('favourite', JSON.stringify(addfavstreamidsave));

            if(typeof addfavstreamidsave[anyname] != "undefined") 
            {
                if(addfavstreamidsave[anyname].length > 0)
                {
                    $("#totalstreams-favourite").html(addfavstreamidsave[anyname].length);    
                } 
                else
                {
                    $("#totalstreams-favourite").html("0");
                }
            } 
            setTimeout(function(){ filterstreamsbyid(categoryid); }, 100);