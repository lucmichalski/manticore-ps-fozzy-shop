/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
var options = {
    series: {
        lines: {
            show: true,
            lineWidth: 1,
            fill: true
        }
    },
    xaxis: {
        mode: "time",
        tickFormatter: function (v, axis) {
            var date = new Date(v);
            if (date.getSeconds() % 20 == 0) {
                var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
                var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
                return hours + ":" + minutes + ":" + seconds;
            } else {
                return "";
            }
        },
        axisLabel: "Time",
        axisLabelUseCanvas: true,
        axisLabelFontSizePixels: 12,
        axisLabelFontFamily: 'Verdana, Arial',
        axisLabelPadding: 10
    },
    yaxis: {
        min: 0,
//        max: 100,       
        tickFormatter: function (v, axis) {
            return Math.round(v*100)/100 + "s";
        }, 
        axisLabel: "Speed loading",
        axisLabelUseCanvas: false,
        axisLabelFontSizePixels: 12,
        axisLabelFontFamily: 'Verdana, Arial',
        axisLabelPadding: 6
    },
    legend: {        
        labelBoxBorderColor: "#fff"
    },
    grid: {                
        backgroundColor: "#fff",
        tickColor: "#ececec",
        borderColor: '#ececec',
    }
};
$(document).ready(function () {
    $(document).ajaxStart(function() {
      $("#ajax_running").remove();
      setTimeout(function(){ $("#ajax_running").hide(); }, 500); 
    });
    dataset = [
        { label: page_loading_time_text, data: dataTimes, color: "#d1e0b9"}
    ];
    $.plot($("#flot-placeholder1"), dataset, options);
    function updateStatistic() { 
         var start_time = new Date().getTime();
         jQuery.get(url_home, '', 
            function(datajson, status, xhr) {
                request_time = new Date().getTime() - start_time;
                if(request_time)
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: true,
        			dataType : "json",
                    data:'&getTimeSpeed=1&request_time='+request_time,
        			success: function(json)
        			{
        			     $('.error_load').remove();
        			     if(json.time) 
                         {
                            var temp_date= new Date(json.time).getTime();
                            var temp = [temp_date, json.value];
                            dataTimes.shift();
                            dataTimes.push(temp);
                            updateSpeedMeter(json.value);
                         }
                         dataset = [
                            { label: page_loading_time_text, data: dataTimes, color: "#d1e0b9" }
                         ];
                         $.plot($("#flot-placeholder1"), dataset, options);
                         setTimeout(updateStatistic, updateInterval);
                         $( "#ajax_running" ).removeClass('sp_hide');
        			},
                    error: function(xhr, status, error)
                    {
                        setTimeout(updateStatistic, updateInterval);         
                    }
                });
                else
                    setTimeout(updateStatistic, updateInterval);
            }
         ).fail(function() {
            setTimeout(updateStatistic, updateInterval);
         });
    }
    updateStatistic();
});