Line_HolePunch
================================

Generic Ajax HolePunch module for Magento
-----------------------------------------



Module Overview
-------------------------

The Line_HolePunch module provides a generic way to handle holepunching dynamic blocks in Magento.  Specifically, this module will be used in conjunction with a full page caching feature.

The module aims to be as flexible as possible.  

Currently:

- Blocks can be marked as requiring a full layout load (ensuring that any required registry objects and layout handles are applied to the layout before being loaded - though please be as conservative as possible with this as it will slow the ajax call down)
- Any block can be marked as dynamic via config.xml
- Routes can be blocked
- Custom placholder templates can be used on a per block basis
	
Usage
-------------------------
After installing the module, it can be enabled in the admin area under the developer section
Configuration options are set in config.xml under the <holepunch> node.  Currently 3 nodes are observed: 

1. Blocks
	Any blocks that are to be dynamic must be declared in this section using the block name as it is defined in layout.xml i.e.
	```
	<top_links>
           <name>top.links</name>
	</top_links>
	```	
	
2. Registry
	Specify any objects which the module should look out for in the registry.  Sometimes these objects will be important for dynamic blocks. Use the following format:
	
	```
	 <registry>
		<maps>
			<product>
				<registry_key>product</registry_key>
				<class_alias>catalog/product</class_alias>
			</product>
			<category>
				<registry_key>current_category</registry_key>
				<class_alias>catalog/category</class_alias>
			</category>
		</maps>
	</registry>
	```

3. Paths
	Here you can let the module know about any routes which should be excluded.  For example, the customer account pages are exluded with the following xml:
	
	```
	 <paths>
		<excluded>
			<customer_account>
				<path>customer/account</path>
			</customer_account>
		</excluded>
	</paths>
	```
	

Feedback
-------------------------
Please forward any feedback to drew@line.uk.com


Copyright
-------------------------
Copyright (c) 2012 Line Digital Limited (http://www.line.uk.com)


License
-------------------------
http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)


Changelog
-------------------------
1.0.0 - Initial Commit