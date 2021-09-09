<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CLI Tools
    |--------------------------------------------------------------------------
    |
    | These are a list of CLI tools that will be installed during onboarding.
    | Any additional CLI tools should be added at the end.
    |
    */

    'cli' => [

        'global' => [

            'xcode' => [
                'name' => 'XCode',
                'description' => 'Installs XCode and git',
                'check' => 'xcode-select -p',
                'command' => 'xcode-select --install',
            ],

            'brew' => [
                'name' => 'Homebrew',
                'description' => '',
                'check' => 'which brew',
                'command' => '/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"',
            ],

            'npm' => [
                'name' => 'npm',
                'description' => '',
                'check' => 'which npm',
                'command' => 'brew install npm',
            ],

//            'cask' => [
//                'name' => 'Cask',
//                'description' => '',
//                'check' => 'brew list cask',
//                'command' => 'brew tap homebrew/cask && brew tap homebrew/cask-versions',
//            ],

            'docker' => [
                'name' => 'Docker',
                'description' => '',
                'check' => 'which docker',
                'command' => 'brew install docker',
            ],

            'awscli' => [
                'name' => 'AWS CLI',
                'description' => '',
                'check' => 'which aws',
                'command' => 'brew install awscli',
            ],

        ],

        'backend' => [

            'php' => [
                'name' => 'PHP',
                'description' => '',
                'check' => 'which php',
                'command' => 'brew install php',
            ],

            'python' => [
                'name' => 'Python',
                'description' => 'Installing Python...',
                'check' => 'which python3',
                'command' => 'brew install python3',
            ],

            'composer' => [
                'name' => 'Composer',
                'description' => '',
                'check' => 'which composer',
                'command' => 'brew install composer',
            ],

            'valet' => [
                'name' => 'Valet',
                'description' => '',
                'check' => 'which valet',
                'command' => 'composer global require laravel/valet && valet domain test',
            ],

            'redis' => [
                'name' => 'Redis',
                'description' => '',
                'command' => 'yes | pecl install -f redis',
            ],

        ],

        'frontend' => [

            'yarn' => [
                'name' => 'yarn',
                'description' => '',
                'check' => 'which yarn',
                'command' => 'brew install yarn',
            ],

            'expo' => [
                'name' => 'expo',
                'description' => '',
                'check' => 'which expo-cli',
                'command' => 'npm install -g expo-cli',
            ],

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Unix Shells
    |--------------------------------------------------------------------------
    |
    | These are a list of Unix shells that can be installed.
    |
    */

    'shell' => [

        'options' => [
            'ohmyzsh' => [
                'name' => 'Oh My Zsh',
                'description' => 'Oh My Zsh is an open source, community-driven framework for managing your zsh configuration. It comes with a bunch of features out of the box and improves your terminal experience.',
                'url' => 'https://github.com/robbyrussell/oh-my-zsh',
                'command' => 'sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)" && php artisan configure:oh-my-zsh',
            ],

            'prezto' => [
                'name' => 'Prezto',
                'description' => 'Prezto is a configuration framework for zsh; it enriches the command line interface environment with sane defaults, aliases, functions, auto completion, and prompt themes.',
                'url' => 'https://github.com/sorin-ionescu/prezto',
                'command' => '
                    git clone --recursive https://github.com/sorin-ionescu/prezto.git "${ZDOTDIR:-$HOME}/.zprezto" && \
                    setopt EXTENDED_GLOB \
                        for rcfile in "${ZDOTDIR:-$HOME}"/.zprezto/runcoms/^README.md(.N); do \
                            ln -s "$rcfile" "${ZDOTDIR:-$HOME}/.${rcfile:t}" \
                        done
                ',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Applications
    |--------------------------------------------------------------------------
    |
    | These are a list of apps that can be selected to be installed by the
    | developer. Some of them are "oneOf", meaning only one of them can be
    | installed, while others are "anyOf", meaning they can install more than
    | one. For each set there will always be an option to install "none".
    |
    */

    'apps' => [

        'database_management' => [
            'group' => 'Database Management',
            'type' => 'oneOf',
            'options' => [
                'dbngin' => [
                    'name' => 'DBngin',
                    'description' => '',
                    'url' => 'https://dbngin.com/',
                    'licensed' => false,
                    'check' => 'dbngin',
                    'command' => 'brew install dbngin',
                ],
                'postgresapp' => [
                    'name' => 'Postgres.app',
                    'description' => '',
                    'url' => 'https://postgresapp.com/',
                    'licensed' => true,
                    'check' => 'postgres',
                    'command' => 'brew install postgres-unofficial',
                ],
            ],

        ],

        'database_client' => [
            'group' => 'Database Client',
            'type' => 'anyOf',
            'options' => [
                'postico' => [
                    'name' => 'Postico',
                    'description' => '',
                    'url' => 'https://eggerapps.at/postico/',
                    'licensed' => true,
                    'check' => 'postico',
                    'command' => 'brew install postico',
                ],
                'tableplus' => [
                    'name' => 'TablePlus',
                    'description' => '',
                    'url' => 'https://tableplus.com/',
                    'licensed' => true,
                    'check' => 'tableplus',
                    'command' => 'brew install tableplus',
                ],
            ],
        ],

        'ide' => [
            'group' => 'IDE',
            'type' => 'anyOf',
            'options' => [
                'phpstorm' => [
                    'name' => 'PHPStorm',
                    'description' => '',
                    'url' => 'https://www.jetbrains.com/phpstorm/',
                    'licensed' => true,
                    'check' => 'phpstorm',
                    'command' => 'brew install phpstorm',
                ],
                'sublime' => [
                    'name' => 'Sublime Text',
                    'description' => '',
                    'url' => 'https://www.sublimetext.com/',
                    'licensed' => true,
                    'check' => 'sublime',
                    'command' => 'brew install sublime-text',
                ],
                'visual_studio' => [
                    'name' => 'Visual Studio Code',
                    'description' => '',
                    'url' => 'https://code.visualstudio.com/',
                    'licensed' => true,
                    'check' => 'visual studio',
                    'command' => 'brew install visual-studio-code',
                ],
            ],
        ],

        'api_client' => [
            'group' => 'API Client',
            'type' => 'anyOf',
            'options' => [
                'paw' => [
                    'name' => 'Paw',
                    'description' => '',
                    'url' => 'https://paw.cloud/',
                    'licensed' => true,
                    'check' => 'paw',
                    'command' => 'brew install paw',
                ],
                'postman' => [
                    'name' => 'Postman',
                    'description' => '',
                    'url' => 'https://www.postman.com/',
                    'licensed' => true,
                    'check' => 'postman',
                    'command' => 'brew install postman',
                ],
            ],
        ],

        'console' => [
            'group' => 'Console IDE',
            'type' => 'oneOf',
            'options' => [
                'iterm2' => [
                    'name' => 'iTerm2',
                    'description' => '',
                    'url' => 'https://iterm2.com/',
                    'licensed' => false,
                    'check' => 'iterm',
                    'command' => 'brew install iterm2',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Repos
    |--------------------------------------------------------------------------
    |
    | These are a list of repos that can be selected to be cloned and set up
    | for the developer.
    |
    */

    'repos' => [

        'core' => [
            'name' => 'Clair Core API',
            'type' => 'laravel',
            'ssh' => 'git@github.com:getclair/core.git',
        ],

        'mobile' => [
            'name' => 'Clair React Native mobile app',
            'type' => 'react-native',
            'ssh' => 'git@github.com:getclair/mobile.git',
        ],

        'embedded-web' => [
            'name' => 'Embedded Web Onboarding',
            'type' => 'react-native',
            'ssh' => 'git@github.com:getclair/embedded-web.git',
        ],

        'getclair' => [
            'name' => 'Getclair.com',
            'type' => 'laravel',
            'ssh' => 'git@github.com:getclair/getclair.com.git',
        ],

    ],

];
