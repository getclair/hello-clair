# Clair Onboarding App

## Commands

"Hello Clair" comes with a set of commands to make environment setup and configuration a breeze for new and existing team members alike.

Command | Description
------- | -------------
`clair hello` | The all-in-one walkthrough for installation
`clair install:cli-tools` | Install CLI tools for `frontend`, `backend`, both, or none
`clair install:apps` | Install various development software and apps
`clair install:repos` | Install and setup Clair projects
`clair configure` | Set up Git credentials

## Configuration

- A global `.gitignore` file is defined and set up for git
- Ability to setup Git author and authorization settings

## Options

### CLI Tools

All CLI tools automatically install for each selected environment. `global` tools are installed regardless. The latest version of each package will be installed. However, if the package already exists on the machine it will be skipped.

Global | Backend | Frontend
------ | ------- | --------
XCode | PHP | yarn
Homebrew | Composer | expo
npm | Valet | 
Cask | Redis | 
Docker | Python | 
AWS CLI | | 

### Unix Shell (one of)

When a Unix shell is selected to be installed, `zsh` is installed first.

- Oh My Zsh (https://github.com/robbyrussell/oh-my-zsh)
    - Adds a "Hello Clair" profile to iTerm2 with custom fonts
    - Zsh and iTerm2 themes
    - .zshrc config
- Prezto (https://github.com/sorin-ionescu/prezto)

### Applications

#### Database Management (one of)

- DBngin (https://dbngin.com/)
- Postgres.app (https://postgresapp.com/)

#### Database Client (any of)

- Postico (https://eggerapps.at/postico/)
- TablePlus (https://tableplus.com/)

#### IDE (any of)

- PHPStorm (https://www.jetbrains.com/phpstorm/)
- Sublime Text (https://www.sublimetext.com/)
- Visual Studio Code (https://code.visualstudio.com/)

#### API Client (any of)

- Paw (https://paw.cloud/)
- Postman (https://www.postman.com/')

#### Console IDE (one of)

- iTerm2 (https://iterm2.com/)

### Projects

Projects are cloned and configured:

- PHP / Laravel apps have packages installed, keys set, and a local URL defined
- React apps have packages installed

#### Repos
- Core API (https://github.com/getclair/core)
- Mobile App (https://github.com/getclair/mobile)
- Embedded Web (https://github.com/getclair/embedded-web)
- Getclair.com (https://github.com/getclair/getclair.com)


## Development

1. Clone the repo locally
2. Run `composer install`

