
About this repository
=====================

This repository contains little plugins and modules made by some contributors, but that
have not a project web site, or/and the creator doesn't maintain it anymore.

They are not stored in the official repository of Jelix, because of various reasons:

   * The core team cannot test them because it need specific tools that they cannot use (a specific database for example)
   * The core team doesn't have the knowledge to maintain it. For example, they don't speak the language for language packs, they don't know the underlaying technology etc..
   * The plugin/module is used only by few projects, and the core team doesn't want to have too many plugins and modules to maintain in the main repository.


Using these components
======================

Since these components are not in the main repository, some of them are not really maintain. There are chance that they are not working for the latest Jelix release. However, you would like to use one of them for your project.

Verify first in their README file or in their identity file (module.xml for example), their compatibility with your Jelix version, and read release notes. Verify also in the issue tracker if there are known issues about it.

**Important: Jelix Team does not guarantee the quality of these components**. No tests are made by the team. You use these components at your own risk.

If something works wrong, **don't hesitate to contribute**, at least by opening a issue ticket.

**Help us to maintain these components!** :-)

Note that all components are referenced on http://booster.jelix.org, our components index for Jelix.

Proposing a component
=====================

You can propose components which meet the reasons described above. There are no really criterias to propose a component. No specific coding style etc. However, if this is a big component, prefer to open a project on github (it's free! :-) ) or somewhere else.

To propose a plugin/module, you can do it by attaching a zip file in an issue, or by cloning the repository and doing a pull request. In any case:

- each commit messages (if this is a pull repquest) should begin with "[name] " where name is the component name.
- your code source should contain a README and a module.xml/plugin.xml file, explaining what it does, to which Jelix it is compatible, dependencies, which known issues the component have, how it should be installed etc..
- all source files should contain licence header and/or a LICENCE file should be present. Only open source components are accepted.

