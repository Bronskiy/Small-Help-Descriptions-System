</main>
</div>
</div>
<script crossorigin src="https://unpkg.com/react@16/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
<script type="text/babel" src="/assets/js/app.js"></script>

<?php if ($uri_segments[1] === 'dashboard'): ?>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script type="text/javascript">
  $('.btn-phone-delete').on('click',function(){
    var phoneDelete = $(this).attr('data-phone');
    var phoneItem = $(this).closest('li');
    console.log(phoneDelete);
    console.log(phoneItem);
    $.ajax({
      type:"GET",
      url:"/api/removePhone?phoneDelete="+phoneDelete,
      success:function(res){
        if(res){
          phoneItem.remove();
          var phoneCount = $('.badge.badge-secondary.badge-pill').text();
          $('.badge.badge-secondary.badge-pill').text(phoneCount-1);
        }else{
        }
      }
    });
  });

  $('#inputCountry').change(function(){
    var countryID = $(this).val();
    if(countryID){
      $.ajax({
        type:"GET",
        url:"/api/get-state-list?country_id="+countryID,
        success:function(res){
          if(res){
            $("#inputState").empty();
            $("#inputCity").empty();
            $("#inputState").append('<option>Province / State</option>');
            $("#inputCity").append('<option>City</option>');
            $.each(res,function(key,value){
              $("#inputState").append('<option value="'+value['id']+'">'+value['name']+'</option>');
            });
          }else{
            $("#inputState").empty();
            $("#inputCity").empty();
          }
        }
      });
    }else{
      $("#inputState").empty();
      $("#inputCity").empty();
    }
  });
  $('#inputState').on('change',function(){
    var stateID = $(this).val();
    if(stateID){
      $.ajax({
        type:"GET",
        url:"/api/get-city-list?state_id="+stateID,
        success:function(res){
          if(res){
            $("#inputCity").empty();
            $.each(res,function(key,value){
              $("#inputCity").append('<option value="'+value['id']+'">'+value['name']+'</option>');
            });
          }else{
            $("#inputCity").empty();
          }
        }
      });
    }else{
      $("#inputCity").empty();
    }
  });
</script>
<?php endif; ?>
</body>
</html>
