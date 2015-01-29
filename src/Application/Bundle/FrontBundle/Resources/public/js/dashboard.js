/**
 * Dashboard class
 * 
 * @returns {Dashboard}
 */
function Dashboard() {

    var selfObj = this;
    var baseUrl = null;

    /**
     * Set the base url.
     * @param {string} base_url
     * 
     */
    this.setBaseUrl = function (base_url) {
        baseUrl = base_url;
    }

    /**
     * 
     * @returns {undefined}
     */
    this.bindAll = function () {
        selfObj.onChangeProjects();
    }
    /**
     * 
     * @returns {undefined}
     */
    this.onChangeProjects = function () {
        $('#projects').change(function () {
            $.blockUI({
                message: 'Please wait...',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff',
                    zIndex: 999999
                }
            });
            var selectedProject = $(this).val();
            if (selectedProject) {
                formatUrl = baseUrl + 'getFormatCount/' + selectedProject;
                cuUrl = baseUrl + 'getCommercialUniqueCount/' + selectedProject;
                totalRecordsUrl = baseUrl + 'getTotalRecords/' + selectedProject;
                $('#formatCount').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    credits: {
                        enabled: false
                    },
                    xAxis: {
                        type: 'category',
                        labels: {
                            rotation: -90,
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Total count per format'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}'
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<span><b>{series.name}: </b></span>',
                        pointFormat: '<span style="color:{point.color}">{point.name}</span><br/><b>Total:</b>{point.y}<br/>'
                    },
                    series: [{
                            name: 'Format',
                            colorByPoint: true,
                            data: (function () {
                                var data;
                                $.ajax({
                                    url: formatUrl,
                                    async: false,
                                    dataType: "json",
                                    type: "GET",
                                    success: function (response) {
                                        data = response;
                                    }
                                });
                                return data;
                            })()
                        }]
                });

                $('#commercialUnique').highcharts({
                    chart: {
                        type: 'column',
                        width: 455,
                        height: 400
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    credits: {
                        enabled: false
                    },
                    xAxis: {
                        type: 'category',
                        labels: {
                            rotation: -90,
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Total count per format'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}'
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<span><b>{series.name}: </b></span>',
                        pointFormat: '<span style="color:{point.color}">{point.name}</span><br/><b>Total:</b>{point.y}<br/>'
                    },
                    series: [{
                            name: 'Commercial/Unique',
                            colorByPoint: true,
                            data: (function () {
                                var data;
                                $.ajax({
                                    url: cuUrl,
                                    async: false,
                                    dataType: "json",
                                    type: "GET",
                                    success: function (response) {
                                        data = response;
                                    }
                                });
                                return data;
                            })()
                        }]
                });
                ////// ajax call to get total records, linear feet for media types.
                $.ajax({
                    type: "GET",
                    url: totalRecordsUrl,
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        if (response) {
                            if (response[0].Audio) {
                                $('#audioTotal').html(response[0].Audio.totalRecords);
                                $('#audiolinear').html(response[0].Audio.linearFeet);
                                $('#audiofile').html(response[0].Audio.fileSize);
                            }
                            if (response[1].Video) {
                                $('#videoTotal').html(response[1].Video.totalRecords);
                                $('#videolinear').html(response[1].Video.linearFeet);
                                $('#videofile').html(response[1].Video.fileSize);
                            }
                            if (response[2].Film) {
                                $('#filmTotal').html(response[2].Film.totalRecords);
                                $('#filmlinear').html(response[2].Film.linearFeet);
                                $('#filmfile').html(response[2].Film.fileSize);
                            }
                        }
                        $.unblockUI();
                    }

                }); // Ajax Call 

            }
        }).change();
    }
}