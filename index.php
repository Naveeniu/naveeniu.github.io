<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <title>D3 Map</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/fontawesome.min.js" integrity="sha512-36dJpwdEm9DCm3k6J0NZCbjf8iVMEP/uytbvaiOKECYnbCaGODuR4HSj9JFPpUqY98lc2Dpn7LpyPsuadLvTyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/styles.css">
        
        <script type="text/javascript" src="js/d3.js"></script>
        <!-- <script type="text/javascript" src="js/modernizr-respond.js"></script> -->
        <script src="https://kit.fontawesome.com/type-your-personal-kitcode-here.js"></script>
    </head>
    <style>
        
    </style>
<body>

    <div class="population_boxWrap">
        <div class="header_top">
            <h2>Analysis Report</h2>
        </div>

        <div class="map_top_wrap">
            <div class="mainMapWrap">
                <div class="main_map">
                    <div class="shortBox">
                        <div id="populationShot">
                            <button 
                                class="btn_gen" 
                                name="GenderBox" 
                                type="button" onclick="shortGender()">
                                <span>Show by gender</span>
                            </button>
                            <button 
                                class="btn_age" 
                                name="AgeBox" 
                                type="button"  onclick="shortAge()">
                                <span>Show by age</span>
                            </button>
                        </div>
                        <div id="mapZoom">
                            <button id="zoom_in">+</button>
                            <button id="zoom_out">-</button>
                        </div>
                    </div>
                    <h4>Tracking Map</h4>
                    <div class="topRightInner"> 
                        <h4>Male and Female Color</h4>
                        <ul>
                            <li><span class="square" style="background: #4bb58d;">&nbsp;</span> Male </li>
                            <li><span class="square" style="background: #ffcf00;">&nbsp;</span> Female </li>
                            <li><span class="square" style="background: #000000;">&nbsp;</span> Pumps </li>
                            <li><span class="square" style="background: #00ff00;">&nbsp;</span> 0-10 </li>
                            <li><span class="square" style="background: #2e3192;">&nbsp;</span> 11-20 </li>
                            <li><span class="square" style="background: #ec008c;">&nbsp;</span> 21-40 </li>
                            <li><span class="square" style="background: #0d004c;">&nbsp;</span> 41-60 </li>
                            <li><span class="square" style="background: #0195ff;">&nbsp;</span> 61-80 </li>
                            <li><span class="square" style="background: #f69679;">&nbsp;</span> >80 </li>
                            <li><span class="square" style="background: #ffffff;">&nbsp;</span> Deaths Location </li>
                            <li><span class="square" style="background: #d90303;">&nbsp;</span> Water Pumps Area </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="top_right_bar">
                <div class="barBandsWrap">
                    <h4>Distribution of Deaths by Sex and Age Overall</h4>
                    <div class="barBandinner">
                        <ul>
                            <li><span class="square" style="background: #4bb58d;">&nbsp;</span> Male </li>
                            <li><span class="square" style="background: #ffcf00;">&nbsp;</span> Female </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="map_bottom_wrap">
            <div class="rangeRoundBandsWrap">
                <h4>Time Line Graph</h4>
            </div>
            <div class="barpieChartWrap">
                <h4>Male VS Female Total Deaths Count</h4>
            </div>
        </div>
        
    </div>

    <script type="text/javascript">
        var width  = 700;
        var height = 600;
        // var w = 100;
        // var h = 100;
        var i;
        var barPadding = 1;

        var div = d3.select("body").append("div") 
        .attr("class", "tooltip")       
        .style("opacity", 0);

        var zoom = d3.behavior.zoom()
        .scaleExtent([1, 10])
        .on("zoom", zoomed);

        var drag = d3.behavior.drag()
        .origin(function(d) { return d; })
        .on("dragstart", dragstarted)
        .on("drag", dragged)
        .on("dragend", dragended);

        var	margin = {top: 30, right: 20, bottom: 30, left: 20},
            w1 = 370 - margin.left - margin.right,
            h1 = 300 - margin.top - margin.bottom;

        var margin1 = {top: 50, right: 50, bottom: 50, left: 50},
            w2 = 450 - margin1.left - margin1.right,
            h2 = 400 - margin1.top - margin1.bottom;

        // var zoom = d3.behavior.zoom().scaleExtent([1, 10]).on("zoom", zoomed);

        var svgContainer = d3.select(".main_map")
                .append("svg")
                //.attr("width", width + margin.left + margin.right)
                //.attr("height", height + margin.top + margin.bottom)
                .attr("height", height)
                .attr("width", width)
                .call(zoom)
                .append("g")
                .attr("class", "svgBox")
                
            // .append("g")
            //.attr("transform", "translate(" + margin.left + "," + margin.right + ")");
            // .attr("transform", "translate(0,0)");
        
        // Parse the date / time
        var	parseDate = d3.time.format("%d-%b").parse;
        var formatTime = d3.time.format("%e %B");

        // Set the ranges
        var	x = d3.time.scale().range([0, w1]);
        var	y = d3.scale.linear().range([h1, 0]);

        // Define the axes
        var	xAxis = d3.svg.axis().scale(x)
            .orient("bottom").ticks(5);

        var	yAxis = d3.svg.axis().scale(y)
            .orient("left").ticks(5);

        // Define the line
        var	valueline = d3.svg.line()
            .x(function(d) { return x(d.date); })
            .y(function(d) { return y(d.death); });
            
        // Adds the svg canvas
        var	chart1 = d3.select(".rangeRoundBandsWrap")
            .append("svg")
                .attr("width", 500)
                .attr("height", 300)
            .append("g")
                .attr("transform", "translate(25,10)");

        var x1 = d3.scale.ordinal()
            .rangeRoundBands([0, w2], .1);
        var y0 = d3.scale.linear().domain([0, 100]).range([h2, 0]),
        y1 = d3.scale.linear().domain([0, 100]).range([h2, 0]);
        var xAxis1 = d3.svg.axis()
            .scale(x1)
            .orient("bottom");

        // create left yAxis
        var yAxisLeft = d3.svg.axis().scale(y0).ticks(4).orient("left");
        // create right yAxis
        var yAxisRight = d3.svg.axis().scale(y1).ticks(4).orient("right");

        var chart2 = d3.select(".barBandsWrap").append("svg")
            .attr("width", 450)
            .attr("height", 400)
        .append("g")
            .attr("class", "graph")
            .attr("transform", "translate(" + margin1.left + "," + margin1.top + ")");
            // d3.select("body").append("p").text("");

        var container = svgContainer.append("g");

        function zoomed() {
            container.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
        }

        function dragstarted(d) {
            d3.event.sourceEvent.stopPropagation();
            d3.select(this).classed("dragging", true);
        }

        function dragged(d) {
            d3.select(this).attr("cx", d.x = d3.event.x).attr("cy", d.y = d3.event.y);
        }

        function dragended(d) {
            d3.select(this).classed("dragging", false);
        }

        function zoomed() {
            svgContainer.attr("transform",
                "translate(" + zoom.translate() + ")" +
                "scale(" + zoom.scale() + ")"
            );
        }

        function interpolateZoom (translate, scale) {
            var self = this;
            return d3.transition().duration(350).tween("zoom", function () {
                var iTranslate = d3.interpolate(zoom.translate(), translate),
                    iScale = d3.interpolate(zoom.scale(), scale);
                return function (t) {
                    zoom
                        .scale(iScale(t))
                        .translate(iTranslate(t));
                    zoomed();
                };
            });
        }

        function zoomClick() {
            var clicked = d3.event.target,
                direction = 1,
                factor = 0.2,
                target_zoom = 1,
                center = [width / 2 + 200, height / 2-100],
                extent = zoom.scaleExtent(),
                translate = zoom.translate(),
                translate0 = [],
                l = [],
                view = {x: translate[0], y: translate[1], k: zoom.scale()};

            d3.event.preventDefault();
            direction = (this.id === 'zoom_in') ? 1 : -1;
            target_zoom = zoom.scale() * (1 + factor * direction);

            if (target_zoom < extent[0] || target_zoom > extent[1]) { return false; }

            translate0 = [(center[0] - view.x) / view.k, (center[1] - view.y) / view.k];
            view.k = target_zoom;
            l = [translate0[0] * view.k + view.x, translate0[1] * view.k + view.y];

            view.x += center[0] - l[0];
            view.y += center[1] - l[1];

            interpolateZoom([view.x, view.y], view.k);
        }

        d3.selectAll('button').on('click', zoomClick);


        //Draw the map
        d3.json("dataset/streets.json", function(data) {
            //var datasets=dataset.split(" ");
            
            for (i=0; i<data.length; i++){
                var dataset=data[i];    
                function isInteger(obj) {
                    return obj%1 === 0
                }

                if(dataset[0] && dataset[1])
                {
                    var sdata = dataset.map(function(item){
                    //if (data[i+j].xy.split(/\s+/)[0]>20){console.log(data[i+j].xy.split(/\s+/)[0]);}
                        return {x: item.x*30, y: item.y*30}
                    });

                    //console.log(sdata);
                    var lineFunction=d3.svg.line()
                        .x(function(d) { return d.x; })
                        .y(function(d) { return height - d.y; })
                        .interpolate("linear");

                    lineFunction(sdata);

                    var svgPath = svgContainer
                        .append("path")
                        .attr("stroke", "black")
                        .attr("stroke-width", "2px")
                        .attr("fill", "none");

                    svgPath.attr("d", lineFunction(sdata));
                        
                }
            }
        });

        //Draw pumps
        d3.csv("dataset/pumps.csv", function(data) {
            //var datasets=dataset.split(" ");
            var sdata = Array.apply(1,{length: 13}).map(function(item,j=1){
                j++;
                return {x: data[j-1].x*30, y: data[j-1].y*30}
            });
            svgContainer.selectAll("rect")
                .data(sdata)
                .enter().append("rect")
                .attr("x", function(d) { return d.x; })
                .attr("y", function(d) { return height-d.y; })
                .attr("width", 8)
                .attr("height",8)
                .style("fill", "black")
                .attr("stroke", '#000')
                .attr("stroke-opacity", 0.8)

            svgContainer.selectAll("text")
                .data(sdata)
                .enter()
                .append("text")
                .attr("x", function(d) { return d.x; })
                .attr("y", function(d) { return height-d.y; })
                
                .attr("font-size", 14)
                .attr("class", '')
                .attr("fill", "#d90303")
                // .text("My text")
                .attr('font-family', 'FontAwesome')
                .text(function(d) { return '\ue06b' });
	    });

        //Add deaths
        d3.csv("dataset/deaths_age_sex.csv", function(data) {
                var sdata = data.map(function(item,j=1){
                j++;
                return {x: data[j-1].x*30, y: data[j-1].y*30, age:data[j-1].age, gender:data[j-1].gender }
            });

            var color = d3.scale.category20b();
            var color = ['#00ff00', '#2e3192', '#ec008c', '#0d004c', '#0195ff', '#f69679']
            svgContainer.selectAll("circle")
                .attr("id","death1")
                .data(sdata)
                .enter().append("circle")
                .attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return height-d.y; })
                .attr("r", 5)
                .style("fill", function(d) { return color[d.age]; })
                .attr("stroke", 'black')
                .attr("stroke-opacity",1);
        });

        //Draw overall chart
        d3.csv("dataset/deaths_age_sex.csv", function(data) {
            var overall = [];
            var ag_index = {};
            for (b=0;b<data.length;b++)
            {
                var a = data[b].age;
                var g = data[b].gender;
                if(!ag_index[a+'-'+g]){
                    ag_index[a+'-'+g] = 1;
                }else{
                    ag_index[a+'-'+g] = ag_index[a+'-'+g] + 1;
                }
            }
            for(i=0;i<200;i++){
                var a = i;
                if(ag_index[a + '-' + 1]){
                    var cm = ag_index[a + '-' + 1];
                    var cf = ag_index[a + '-' + 0];
                    overall.push({age: a, male: cm, female: cf});
                }
            }
        
        x1.domain(overall.map(function(d) { return d.age; }));
        y0.domain([0, d3.max(overall, function(d) { 
            if(d.male>d.female)
            {return d.male;}else{return d.female;} })]);

        y1.domain([0, d3.max(overall, function(d) { 
            if(d.male>d.female)
            {return d.male;}else{return d.female;} })]);
        
        chart2.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + h2 + ")")
            .call(xAxis1)
            .append("text")
            .attr("x", 6)
            .attr("dx", "38em")
            .style("text-anchor", "begin")
            .text("Age");

        chart2.append("g")
            .attr("class", "y axis axisLeft")
            .attr("transform", "translate(0,0)")
            .call(yAxisLeft)
        .append("text")
        .attr("x", 10)
            .attr("y", 15)
            .attr("dy", "-2em")
            .style("text-anchor", "end")
            .text("Deaths");
        
        bars = chart2.selectAll(".bar").data(overall).enter();

        bars.append("rect")
            .attr("class", "bar1")
            .attr("x", function(d) { return x1(d.age); })
            .attr("width", x1.rangeBand()/2)
            .attr("y", function(d) { return y0(d.male); })
            .attr("height", function(d,i,j) { return h2 - y0(d.male); }); 

        bars.append("rect")
            .attr("class", "bar2")
            .attr("x", function(d) { return x1(d.age) + x1.rangeBand()/2; })
            .attr("width", x1.rangeBand() / 2)
            .attr("y", function(d) { return y1(d.female); })
            .attr("height", function(d,i,j) { return h2 - y1(d.female); }); 


        chart2.selectAll("text.bar")
        .data(overall)
        .enter().append("text")
            .text(function(d) {
                return d.female;
            })
            .attr("text-anchor", "middle")
            .attr("x", function(d, i) {
                return x1(d.age) + x1.rangeBand()/1.35;
                //return i * (w2 / overall.length) + (w2 / overall.length - 1) / 2;
            })
            .attr("y", function(d) {
                //return h2 - (d * 4) + 14;
                return y1(d.female)-2;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("fill", "gray");

            chart2.selectAll("text.bar")
                .data(overall)
                .enter().append("text")
            .text(function(d) {
                    return d.male;
            })
            .attr("text-anchor", "middle")
            .attr("x", function(d, i) {
                    return x1(d.age) + x1.rangeBand()/4;
                    //return i * (w2 / overall.length) + (w2 / overall.length - 1) / 2;
            })
            .attr("y", function(d) {
                    //return h2 - (d * 4) + 14;
                    return y0(d.male)-2;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("fill", "gray");
        });

        function type(d) {
            d.male = +d.female;
            return d;
        }

        function shortAge(){
            d3.selectAll("circle").remove();
                d3.csv("dataset/deaths_age_sex.csv", function(data) {
                    var sdata = data.map(function(item,j=1){
                    j++;
                    return {x: data[j-1].x*30, y: data[j-1].y*30, age:data[j-1].age, gender:data[j-1].gender }
                });

                //var color = d3.scale.category20b();
                //var color = ['#edf8fb', '#ccece6', '#99d8c9', '#66c2a4', '#2ca25f', '#006d2c']
                var color = ['#00ff00', '#2e3192', '#ec008c', '#0d004c', '#0195ff', '#f69679']
                svgContainer.selectAll("circle")
                    .data(sdata)
                    .enter().append("circle")
                    .attr("cx", function(d) { return d.x; })
                    .attr("cy", function(d) { return height-d.y; })
                    .attr("r", 5)
                    .style("fill", function(d) { return color[d.age]; })
                    .attr("stroke", 'black')
                    .attr("stroke-opacity",1);
            });
        }

        function shortGender(){
            d3.selectAll("circle").remove();
            d3.csv("dataset/deaths_age_sex.csv", function(data) {
                var sdata = data.map(function(item,j=1){
                j++;
                return {x: data[j-1].x*30, y: data[j-1].y*30, age:data[j-1].age, gender:data[j-1].gender }
            });

            //var color = d3.scale.category20b();
            var color = ["#4bb58d","#ffcf00"]

            svgContainer.selectAll("circle")
                .data(sdata)
                .enter().append("circle")
                .attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return height-d.y; })
                .attr("r", 5)
                .style("fill", function(d) { return color[d.gender]; })
                .attr("stroke", "black")
                .attr("stroke-width", "1px")
            });
        }

        //Draw timeline
        d3.csv("dataset/deathdays.csv",function(data){
            console.log(data);
            var sdata = data.map(function(item,j=1){
                j++;
                // return {x: data[j-1].x*30, y: data[j-1].y*30, age:data[j-1].age, mf:data[j-1].gender }
                
                return {date: data[j-1].date, death: Number(data[j-1].deaths)}
            });
            sdata.forEach(function(d) {
                d.date = parseDate(d.date);
                d.death = +d.death;
            });
            //console.log(sdata);
            // Scale the range of the data
            x.domain(d3.extent(sdata, function(d) { return d.date; }));
            y.domain([0, d3.max(sdata, function(d) { return d.death; })]);

            // Add the valueline path.
            chart1.append("path")
                .attr("class", "line")
                .attr("d", valueline(sdata))
                .attr("stroke", "grey")
                .attr("stroke-width", "2px");

            chart1.selectAll("dot")  
                    .data(sdata)     
                .enter().append("ellipse")                 
                    .attr("cx", function(d) { return x(d.date); })     
                    .attr("cy", function(d) { return y(d.death); })
                    .attr("rx", 3.5) 
                    .attr("ry", 3.5)
                    .style("fill", "white") 
                    .attr("stroke", 'steelblue')
         
                /*.on("mouseover", function(d) {    
                    div.transition()    
                        .duration(200)    
                        .style("opacity", .9);    
                    div .html(formatTime(d.date) + "<br/>"  + d.death)  
                        .style("left", (d3.event.pageX) + "px")   
                        .style("top", (d3.event.pageY - 28) + "px");  
                    })  */        
            .on("mouseout", function(d) {   
                div.transition()    
                    .duration(500)    
                    .style("opacity", 0);
                })
            .on("click",function(d, i){
                console.log(d,i);
                chart2.selectAll().remove();
                chart2.selectAll("g").remove();
                chart2.selectAll(".bar").remove();
                chart2.selectAll("rect").remove();
                chart2.selectAll("text.bar").remove();
                chart2.selectAll("text").remove();
                var l=0;
                for (a=0; a<=i; a++){
                    l= l+ sdata[a].deaths;
                }
                d3.csv("dataset/deaths_age_sex.csv", function(data) { 
                    var sdata = data.map(function(item,j=1){
                    j++;
                    return {x: data[j-1].x*30, y: data[j-1].y*30, age:data[j-1].age, gender:data[j-1].gender}
                    
                });

                var color = d3.scale.category20b();
                svgContainer.selectAll("circle")
                    .attr("id","death1")
                    .data(sdata)
                    .enter().append("circle")
                    .attr("cx", function(d) { return d.x; })
                    .attr("cy", function(d) { return height-d.y; })
                    .attr("r", 5)
                    .style("fill", "gray");

                var overall = [];
                var ag_index = {};
                for (b=0;b<data.length;b++)
                {
                    var a = data[b].age;
                    var g = data[b].gender;
                    if(!ag_index[a+'-'+g]){
                        ag_index[a+'-'+g] = 1;
                    }else{
                        ag_index[a+'-'+g] = ag_index[a+'-'+g] + 1;
                    }
                }
                for(i=0;i<200;i++){
                    var a = i;
                    if(ag_index[a + '-' + 1]){
                        var cm = ag_index[a + '-' + 1];
                        var cf = ag_index[a + '-' + 0];
                        overall.push({age: a, male: cm, female: cf});
                    }
                    
                }

            x1.domain(overall.map(function(d) { return d.age}));
            y0.domain([0, d3.max(overall, function(d) { 
                    if(d.male>d.female)
                    {return d.male;}else{return d.female;} })]);

                y1.domain([0, d3.max(overall, function(d) { 
                    if(d.male>d.female)
                {return d.male;}else{return d.female;} })]);

            //y1.domain([0, d3.max(overall, function(d) { return d.male; })]);
            chart2.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + h2 + ")")
                .call(xAxis1)
                .append("text")
            .attr("x", 6)
            .attr("dx", "38em")
            .style("text-anchor", "begin")
            .text("Age");

           

            chart2.append("g")
            .attr("class", "y axis axisLeft")
            .attr("transform", "translate(0,0)")
            .call(yAxisLeft)
            .append("text")
            .attr("x", 10)
            .attr("y", 15)
            .attr("dy", "-2em")
            .style("text-anchor", "end")
            .text("Deaths");

            bars = chart2.selectAll(".bar").data(overall).enter();
            
            bars.append("rect")
                .attr("class", "bar1")
                .attr("x", function(d) { return x1(d.age); })
                .attr("width", x1.rangeBand()/2)
                .attr("y", function(d) { return y0(d.male); })
            .attr("height", function(d,i,j) { return h2 - y0(d.male); }); 

            bars.append("rect")
                .attr("class", "bar2")
                .attr("x", function(d) { return x1(d.age) + x1.rangeBand()/2; })
                .attr("width", x1.rangeBand() / 2)
                .attr("y", function(d) { return y1(d.female); })
            .attr("height", function(d,i,j) { return h2 - y1(d.female); }); 

            chart2.selectAll("text.bar")
            .data(overall)
            .enter().append("text")
                .text(function(d) {
                    return d.female;
                })
                .attr("text-anchor", "middle")
                .attr("x", function(d, i) {
                    return x1(d.age) + x1.rangeBand()/1.35;
                    //return i * (w2 / overall.length) + (w2 / overall.length - 1) / 2;
                })
                .attr("y", function(d) {
                    //return h2 - (d * 4) + 14;
                    return y1(d.female)-2;
                })
                .attr("font-family", "sans-serif")
                .attr("font-size", "11px")
                .attr("fill", "gray");

                chart2.selectAll("text.bar")
                .data(overall)
                .enter().append("text")
                .text(function(d) {
                    return d.male;
                })
                .attr("text-anchor", "middle")
                .attr("x", function(d, i) {
                    return x1(d.age) + x1.rangeBand()/4;
                    //return i * (w2 / overall.length) + (w2 / overall.length - 1) / 2;
                })
                .attr("y", function(d) {
                    //return h2 - (d * 4) + 14;
                    return y0(d.male)-2;
                })
                .attr("font-family", "sans-serif")
                .attr("font-size", "11px")
                .attr("fill", "gray");
            });

            function type(d) {
                d.male = +d.female;
                return d;
            }
        })

        .on("mouseover",function(d, i){
            //d3.select("svg").append("g").selectAll("circle").remove();
            d3.selectAll("circle").remove();
            var l=0;
                for (a=0; a<=i; a++){
                    l= l+ sdata[a].death;
            }
            div.transition()    
                .duration(0)    
                .style("opacity", .9);    
            div .html(formatTime(d.date) + "<br/>"  + d.death + " total:"+l)  
                .style("left", (d3.event.pageX) + "px")   
                .style("top", (d3.event.pageY - 28) + "px"); 

            d3.csv("dataset/deaths_age_sex.csv", function(data) {
                var sdata1 = data.map(function(item,j=1){
                j++;
                return {x: data[j-1].x*30, y: data[j-1].y*30, age:data[j-1].age, gender:data[j-1].gender }
            });

            var color = ['#b2182b', '#ef8a62', '#fddbc7', '#d1e5f0', '#67a9cf', '#2166ac']
                svgContainer.selectAll("circle")
                    .data(sdata1)
                    .enter().append('circle')
                    .attr("cx", function(d) { return d.x; })
                    .attr("cy", function(d) { return height-d.y; })
                    .attr("r", 5)
                    .style("fill", function(d) { return color[d.age]; })
                    .attr("stroke", "black")
                    .attr("stroke-width", "1px")
                });
        });
       
        // Add the X Axis
        chart1.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + h1 + ")")
            .call(xAxis);

        // Add the Y Axis
        chart1.append("g")
            .attr("class", "y axis")
            .call(yAxis)
            .append("text")
        .attr("x", -25)
            .attr("y", -2)
            .text("Deaths");
        });
    </script>
    <script>
        var piewidth = 400,
            pieheight = 300,
            radius = Math.min(piewidth, pieheight) / 2;

        var color = d3.scale.ordinal()
            .range(["#4bb58d", "#ffcf00", "#ec008c"]);

        var arc = d3.svg.arc()
            .outerRadius(radius - 10)
            .innerRadius(0);

        var pie = d3.layout.pie()
            .sort(null)
            .value(function(d) { return d.population; });

        var svg = d3.select(".barpieChartWrap").append("svg")
            .attr("width", piewidth)
            .attr("height", pieheight)
        .append("g")
            .attr("transform", "translate(" + piewidth / 2 + "," + pieheight / 2 + ")");

            
        d3.csv("dataset/deaths_age_sex.csv", function(data) {
            // data.forEach(function(d) {
            //     d.population = +d.population;
            // });

            var overall = [{gender: 'Female', population: 0}, {gender: 'Male', population: 0}];
            for (b=0;b<data.length;b++)
            {
                var g = data[b].gender;
                if(g == 0){
                   overall[0]['population'] = overall[0]['population'] + 1 ;
                }
                if(g == 1){
                   overall[1]['population'] = overall[1]['population'] + 1;
                   
                }
            }

            var malePercentage = (overall[1]['population'] / (overall[0]['population']+overall[1]['population'])) * 100;
            var femalePercentage = (overall[0]['population'] / (overall[0]['population']+overall[1]['population'])) * 100;
            overall[1]['percentage'] = malePercentage.toFixed(1) + '%';
            overall[0]['percentage'] = femalePercentage.toFixed(1) + '%';
            
        // console.log(overall);
        // append a group
        var pieg = svg.selectAll(".arc")
            .data(pie(overall))
            .enter().append("g")
            .attr("class", "arc");

        // append path, the pie for each age
        pieg.append("path")
            .attr("d", arc)
            .style("fill", function(d) { return color(d.data.gender); });
    
        // add text
        pieg.append("text")
            .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
            .attr("dy", ".35em")
            .attr("class", "pieChatInnter")
            .style("text-anchor", "middle")
            .style("fill", "#000")
            // .text(function(d) { return d.data.gender + ' ' + '(' + d.data.population + ')'; });
            .html(function (d) {
					// return d.amount + ' (' + d.value + '% )';
					// console.log(d);
					var html = '<tspan class="title" dx="10" dy="-12"> ' + d.data.gender + ' </tspan>';
					html += '<tspan class="population" dx="-50" dy="30"> (' + d.data.percentage + ') </tspan>';
					return html;
				});
        });

    </script>


</body>
</html>