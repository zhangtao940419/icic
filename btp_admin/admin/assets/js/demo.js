/**
 * jeDate 演示
 */
    var enLang = {                            
        name  : "en",
        month : ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
        weeks : [ "SUN","MON","TUR","WED","THU","FRI","SAT" ],
        times : ["Hour","Minute","Second"],
        timetxt: ["Time","Start Time","End Time"],
        backtxt:"Back",
        clear : "Clear",
        today : "Now",
        yes   : "Confirm",
        close : "Close"
    }

    //常规选择
    jeDate("#test04",{
        theme: {
            bgcolor: "#00A680",
            pnColor: "#00DDAA"
        },
        festival:true,
        minDate:"1900-01-01",              //最小日期
        maxDate:"2099-12-31",              //最大日期
        method:{
            choose:function (params) {
                
            }
        },
        format: "YYYY-MM-DD hh:mm:ss"
    });  

    jeDate("#test05", {
        theme: {
            bgcolor: "#00A680",
            pnColor: "#00DDAA"
        },
        festival: true,
        minDate: "1900-01-01", //最小日期
        maxDate: "2099-12-31", //最大日期
        method: {
            choose: function (params) {

            }
        },
        format: "YYYY-MM-DD hh:mm:ss"
    });
 


   

   
   

    


    
    