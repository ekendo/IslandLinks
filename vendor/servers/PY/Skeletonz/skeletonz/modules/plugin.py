class GenericPlugin:

    def addToSiteEditTemplate(self, template, on_init):
        """
        Intent:
          Give ability to decorate templates with new style sheets or JavaScript code.
        Arguments:
          template:
            An object from skeletonz.modules.template.Template. Has very useful functions such as getHeader etc.
          on_init:
            True if this method is called from an __init__ function.
        """
        raise NotImplementedError()

    def addToSiteTemplate(self, template, on_init):
        """
        Intent:
          Give ability to decorate templates with new style sheets or JavaScript code.
        Arguments:
          template:
            An object from skeletonz.modules.template.Template. Has very useful functions such as getHeader etc.
          on_init:
            True if this method is called from an __init__ function.
        """
        raise NotImplementedError()

    def addToController(self, root_controller):
        """
        Intent:
          Can be used to add things to the controller and to serve static files.
        Arguments:
          root_controller:
            AmiJS root controller. It's specification is located in amilib.amiweb.amiweb -> Class RootController
        """
        raise NotImplementedError()

    def createStructure(self):
        """
        Intent:
          The ability to create the plug-in tables in the database.
        Called from:
          skeletonz.model.db_structure
        """
        raise NotImplementedError()

    def dropStructure(self):
        """
        Intent:
          The ability to drop the plug-in tables in the database.
        Called from:
          skeletonz.model.db_structure
        """
        raise NotImplementedError()

    def optionValue(self, option_name):
        option = [option for option in self.PLUGIN_OPTIONS if option.name == option_name][0]
        return option.getValue() and option.getValue() or option.getDefault()
