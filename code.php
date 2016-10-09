<section id="hung"></section>

<form onsubmit="return processCode(this)">
	<textarea name="code"></textarea>
	<button type="submit">Submit</button>
</form>

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

	function processCode(el)
	{
		console.log(el);

		document.getElementById('hung').innerHTML = el.code.value;

		var data = getAllElementsWithAttribute('data-reactid');
	
		data.forEach(function(e){
			e.removeAttribute('data-reactid')
		})

		el.code.value = document.getElementById('hung').innerHTML;

		return false;
	}
	
	
	
</script>