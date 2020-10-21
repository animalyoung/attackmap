<!DOCTYPE html>
<head>
<meta charset="utf-8">
<style>
body {
  margin: 0;
  padding: 0;
  background: black;
}

#titlediv {
  font-family: monospace;
  text-align: center;
  font-size:48px;
  position:fixed;
  width:100%;
  height:50px;
  color:white;
  background-color:black;
  padding:5px;
  top:0px;
  overflow-y: auto;
}

#attackdiv {
  font-family: monospace;
  font-size:10px;
  position:fixed;
  width:50%;
  height:100px;
  color:white;
  background-color:black;
  padding:5px;
  bottom:0px;
  overflow-y: auto;
}

#container1 {
  position: relative;
  width: 100vw;
  height: 100vh;
  max-width:100%;
  max-height:100%
}

#about {
  display: hidden;
}

#aboutdiv {
  text-align:right;
  width:100px;
  height:100px;
  top:0px;
  right:0px;
  position:fixed;
  padding:10px;
  color: white;
}

#ccdiv {
  text-align:right;
  width:100px;
  height:20px;
  bottom:0px;
  right:0px;
  position:fixed;
  padding:5px;
  color: white;
}

#about {display:none;}

/* Overlay */
#simplemodal-overlay {background-color:#000;}

/* Container */
#simplemodal-container {height:460px; width:600px; color:#bbb; background-color:#333; border:4px solid #444; padding:12px;}
#simplemodal-container .simplemodal-data {padding:8px;}
#simplemodal-container code {background:#141414; border-left:3px solid #65B43D; color:#bbb; display:block; font-size:12px; margin-bottom:12px; padding:4px 6px 6px;}
#simplemodal-container a {color:#ddd;}
#simplemodal-container a.modalCloseImg {background:url(../img/basic/x.png) no-repeat; width:25px; height:29px; display:inline; z-index:3200; position:absolute; top:-15px; right:-16px; cursor:pointer;}
#simplemodal-container h3 {color:#84b8d9;}
#simplemodal-container a.modalCloseImg {
  background:url(x.png) no-repeat; /* adjust url as required */
  width:25px;
  height:29px;
  display:inline;
  z-index:3200;
  position:absolute;
  top:-15px;
  right:-18px;
  cursor:pointer;
}

</style>

<script src="https://d3js.org/d3.v3.min.js"></script>
<script src="https://d3js.org/d3.geo.projection.v0.min.js"></script>
<script src="https://d3js.org/topojson.v1.min.js"></script>
<script src="https://datamaps.github.io/scripts/datamaps.world.min.js?v=1"></script>
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="{{ asset('js/jquery.simplemodal-1.4.4.js') }}"></script>

<script>
function about() {
  $("#about").modal();
}
</script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

</head>

<body>


  <center><div id="container1"></div></center>
  <div id="titlediv">Projectgroup Attack-map</div>
  <div id="attackdiv"></div>


  <div id="ccdiv">

  </div>



  <!-- Use Hash-Bang to maintain scroll position when closing modal -->
  <a href="#!" class="modal-close" title="Close this modal"
      data-dismiss="modal" data-close="Close">&times;</a>
</section>


  <script>


    function FixedQueue( size, initialValues ){
      initialValues = (initialValues || []);
      var queue = Array.apply( null, initialValues );
      queue.fixedSize = size;
      queue.push = FixedQueue.push;
      queue.splice = FixedQueue.splice;
      queue.unshift = FixedQueue.unshift;
      FixedQueue.trimTail.call( queue );
      return( queue );
    }

    FixedQueue.trimHead = function(){
      if (this.length <= this.fixedSize){ return; }
      Array.prototype.splice.call( this, 0, (this.length - this.fixedSize) );
    };

    FixedQueue.trimTail = function(){
      if (this.length <= this.fixedSize) { return; }
      Array.prototype.splice.call( this, this.fixedSize, (this.length - this.fixedSize)
      );
    };

    FixedQueue.wrapMethod = function( methodName, trimMethod ){
      var wrapper = function(){
        var method = Array.prototype[ methodName ];
        var result = method.apply( this, arguments );
        trimMethod.call( this );
        return( result );
      };
      return( wrapper );
    };

    FixedQueue.push = FixedQueue.wrapMethod( "push", FixedQueue.trimHead );
    FixedQueue.splice = FixedQueue.wrapMethod( "splice", FixedQueue.trimTail );
    FixedQueue.unshift = FixedQueue.wrapMethod( "unshift", FixedQueue.trimTail );

    var rand = function(min, max) {
        return Math.random() * (max - min) + min;
    };

    var getRandomCountry = function(countries, weight) {

        var total_weight = weight.reduce(function (prev, cur, i, arr) {
            return prev + cur;
        });

        var random_num = rand(0, total_weight);
        var weight_sum = 0;

        for (var i = 0; i < countries.length; i++) {
            weight_sum += weight[i];
            weight_sum = +weight_sum.toFixed(2);

            if (random_num <= weight_sum) {
                return countries[i];
            }
        }

    };

    // need to make this dynamic since it is approximated from sources

    var countries = [9,22,29,49,56,58,78,82,102,117,139,176,186] ;
    var weight = [0.000,0.001,0.004,0.008,0.009,0.037,0.181,0.002,0.000,0.415,0.006,0.075,0.088];

    // the fun begins!
    //
    // pretty simple setup ->
    // * make base Datamap
    // * setup timers to add random events to a queue
    // * update the Datamap

    var map = new Datamap({

        scope: 'world',
        element: document.getElementById('container1'),
        projection: 'winkel3',
        // change the projection to something else only if you have absolutely no cartographic sense

        fills: { defaultFill: 'black', },

        geographyConfig: {
          dataUrl: null,
          hideAntarctica: true,
          borderWidth: 0.75,
          borderColor: '#4393c3',
          popupTemplate: function(geography, data) {
            return '<div class="hoverinfo" style="color:white;background:black">' +
                   geography.properties.name + '</div>';
          },
          popupOnHover: true,
          highlightOnHover: false,
          highlightFillColor: 'black',
          highlightBorderColor: 'rgba(250, 15, 160, 0.2)',
          highlightBorderWidth: 2
        },

      })

    // we read in a modified file of all country centers
    var centers = [] ;
    d3.tsv("{{ asset('csv/country_centroids_primary.csv') }}", function(data) { centers = data; });
    d3.csv("{{ asset('csv/samplatlong.csv') }}", function(data) { slatlong = data; });
    d3.csv("{{ asset('csv/cnlatlong.csv') }}", function(data) { cnlatlong = data; });
    // setup structures for the "hits" (arcs)
    // and circle booms

    var hits = FixedQueue( 7, [  ] );
    var boom = FixedQueue( 7, [  ] );

    // we need random numbers and also a way to build random ip addresses
    function getRandomInt(min, max) {return Math.floor(Math.random() * (max - min + 1)) + min;}
    function getOctet() {return Math.round(Math.random()*255);}
    function randomIP () { return(getOctet() + '.' + getOctet() + '.' + getOctet() + '.' + getOctet()); }
    function getStroke() {return Math.round(Math.random()*100);}
    function getDestination() {return Math.round(Math.random()*100);}

    // doing this a bit fancy for a hack, but it makes it
    // easier to group code functions together and have variables
    // out of global scope
    var attacks = {

        attacks: @json($attacks),
        interval: 900,
        init: function(position){
           setTimeout(
               jQuery.proxy(this.getData, this, position),
               this.interval
           );
        },

        getLocation: function(shortcode){
          jQuery.each(cnlatlong, function( index, value ) {
              if(value.ISO3136 === shortcode){
                return value;
              }
              });
        },

       getData: function(position) {
          if(position < this.attacks.length){
           var self = this;




           // use strokeColor to set arc line color

           var srclat = this.attacks[position].origin.lat;
           var srclong = this.attacks[position].origin.long;
           var dstlat = this.attacks[position].destination.lat;
           var dstlong =this.attacks[position].destination.long;

           var srccountry = this.attacks[position].origin.country;
           var attackdiv_slatlong = this.attacks[position].destination.country;

           switch (this.attacks[position].type) {
              case 'DDOS':
                var strokeColor='red';
                break;
              case 'SPAM':
                var strokeColor='blue';
                break;
              case 'Banking':
                var strokeColor='green';
                break;
              default:
                var strokeColor='orange';
                break;
            }



           hits.push( { origin : { latitude: +srclat, longitude: +srclong },
                        destination : { latitude: +dstlat, longitude: +dstlong } } );
           map.arc(hits, {strokeWidth: 2, strokeColor: strokeColor});

           // add boom to the bubbles queue

           boom.push( { radius: 7, latitude: +dstlat, longitude: +dstlong,
                        fillOpacity: 0.5, attk: this.attacks[position].type} );
           map.bubbles(boom, {
                popupTemplate: function(geo, data) {
                  return '<div class="hoverinfo">' + data.attk + '</div>';
                }
            });

           // update the scrolling attack div
           $('#attackdiv').append(srccountry + " (" + this.attacks[position].origin.ip + ") " +
                                  " <span style='color:red'>attacks</span> " +
                                  attackdiv_slatlong+  " (" + this.attacks[position].destination.ip + ") " +
                                  " <span style='color:steelblue'>(" + this.attacks[position].type + ")</span> " +
                                  "<br/>");
           $('#attackdiv').animate({scrollTop: $('#attackdiv').prop("scrollHeight")}, 500);


           position++;
           this.init(position) ;
         }
         else {
           location.reload();
         }

       },

    };

    // start the ball rolling!
    attacks.init(0);

    // lazy-dude's responsive window
    d3.select(window).on('resize', function() { location.reload(); });

</script>

</body>
</html>
