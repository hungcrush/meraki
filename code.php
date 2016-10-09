<section id="hung">
	
		
</section>

<script type="text/javascript">
	function getAllElementsWithAttribute(attribute)
	{
	  var matchingElements = [];
	  var allElements = document.getElementsByTagName('*');
	  for (var i = 0, n = allElements.length; i < n; i++)
	  {
	    if (allElements[i].getAttribute(attribute) !== null)
	    {
	      // Element exists with attribute. Add to array.
	      matchingElements.push(allElements[i]);
	    }
	  }
	  return matchingElements;
	}
	
	var data = getAllElementsWithAttribute('data-reactid');
	
	data.forEach(function(e){
		e.removeAttribute('data-reactid')
	})
	
</script>