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
                'description' => 'Installing XCode and git...',
                'check' => 'xcode-select -p',
                'command' => 'xcode-select --install',
            ],

            'brew' => [
                'name' => 'Homebrew',
                'check' => 'which brew',
                'command' => '/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"',
            ],

            'npm' => [
                'name' => 'npm',
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
                'check' => 'which docker',
                'command' => 'brew install docker',
            ],

            'awscli' => [
                'name' => 'AWS CLI',
                'check' => 'which aws',
                'command' => 'brew install awscli',
            ],

            'mas' => [
                'name' => 'mas-cli',
                'check' => 'which mas',
                'command' => 'brew install mas',
            ],

        ],

        'backend' => [

            'php' => [
                'name' => 'PHP',
                'check' => 'which php',
                'command' => 'brew install php',
            ],

            'python' => [
                'name' => 'Python',
                'check' => 'which python3',
                'command' => 'brew install python3',
            ],

            'composer' => [
                'name' => 'Composer',
                'check' => 'which composer',
                'command' => 'brew install composer',
            ],

            'valet' => [
                'name' => 'Valet',
                'check' => 'which valet',
                'command' => 'composer global require laravel/valet && valet domain test',
                'tasks' => [
                    'redis' => [
                        'name' => 'Redis',
                        'description' => 'Installing Redis...',
                        'command' => 'yes | pecl install -f redis',
                    ],
                ],
            ],

        ],

        'frontend' => [

            'yarn' => [
                'name' => 'yarn',
                'check' => 'which yarn',
                'command' => 'brew install yarn',
            ],

            'expo' => [
                'name' => 'expo',
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
                'description' => 'Installing Oh My Zsh and configurations...',
                'url' => 'https://github.com/robbyrussell/oh-my-zsh',
                'check' => 'which zsh',
                'command' => 'sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"',
                'tasks' => [
                    [
                        'description' => 'Adding Zsh theme...',
                        'source' => resource_path('config/cobalt2-custom.zsh-theme'),
                        'destination' => home_path('.oh-my-zsh/themes/cobalt2-clair.zsh-theme'),
                    ],
                    [
                        'description' => 'Adding iTerm2 theme...',
                        'source' => resource_path('config/iTerm2-custom.zsh'),
                        'destination' => home_path('.oh-my-zsh/custom/iTerm2-clair.zsh'),
                    ],
                    [
                        'description' => 'Adding .zshrc...',
                        'source' => resource_path('config/.zshrc'),
                        'destination' => home_path('/.zshrc-tmp'),
                    ],
                    [
                        'description' => 'Configuring shell aliases...',
                        'command' => "echo 'alias python2=\"/usr/bin/python\"' >> ~/.zshrc && echo 'alias python=\"/usr/local/bin/python3\"' >> ~/.zshrc && source ~/.zshrc",
                    ],
                    [
                        'description' => 'Installing Powerline...',
                        'command' => 'pip3 install iterm2 && pip3 install --user powerline-status && cd ~ && git clone https://github.com/powerline/fonts && cd fonts && ./install.sh && cd ~',
                    ],
                    [
                        'description' => 'Set iTerm2 default profile...',
                        'source' => resource_path('scripts/default-profile.py'),
                        'destination' => home_path('/Library/Application Support/iTerm2/Scripts/AutoLaunch/clair-profile.py'),
                    ],
                    [
                        'description' => 'Enable iTerm2 Python API...',
                        'command' => 'clair configure:iterm2',
                    ],
                ],
            ],

            'prezto' => [
                'name' => 'Prezto',
                'url' => 'https://github.com/sorin-ionescu/prezto',
                'check' => 'which prezto',
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
                    'url' => 'https://dbngin.com/',
                    'licensed' => false,
                    'check' => 'dbngin',
                    'command' => 'brew install dbngin',
                ],
                'postgresapp' => [
                    'name' => 'Postgres.app',
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
                    'url' => 'https://eggerapps.at/postico/',
                    'licensed' => true,
                    'check' => 'postico',
                    'command' => 'brew install postico',
                ],
                'tableplus' => [
                    'name' => 'TablePlus',
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
                    'url' => 'https://www.jetbrains.com/phpstorm/',
                    'licensed' => true,
                    'check' => 'phpstorm',
                    'command' => 'brew install phpstorm',
                    'tasks' => [
                        [
                            'description' => 'Installing Jetbrains Toolbox...',
                            'command' => 'brew install jetbrains-toolbox',
                        ],
                    ],
                ],
                'sublime' => [
                    'name' => 'Sublime Text',
                    'url' => 'https://www.sublimetext.com/',
                    'licensed' => true,
                    'check' => 'sublime',
                    'command' => 'brew install sublime-text',
                ],
                'visual_studio' => [
                    'name' => 'Visual Studio Code',
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
                    'url' => 'https://paw.cloud/',
                    'licensed' => true,
                    'check' => 'paw',
                    'command' => 'brew install paw',
                ],
                'postman' => [
                    'name' => 'Postman',
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
                    'url' => 'https://iterm2.com/',
                    'licensed' => false,
                    'check' => 'iterm',
                    'command' => 'brew install iterm2',
                ],
            ],
        ],

        'development' => [
            'group' => 'development tools',
            'type' => 'anyOf',
            'options' => [
                'github_desktop' => [
                    'name' => 'Github Desktop',
                    'url' => 'https://desktop.github.com/',
                    'licensed' => false,
                    'check' => 'github',
                    'command' => 'brew install github',
                ],

                'jira_desktop' => [
                    'name' => 'Jira Cloud for Mac',
                    'url' => 'https://www.atlassian.com/software/jira/mac',
                    'licensed' => false,
                    'check' => 'jira',
                    'command' => 'mas purchase 1475897096',
                ],

                'stoplight' => [
                    'name' => 'Stoplight Studio',
                    'url' => 'https://stoplight.io/studio/',
                    'licensed' => false,
                    'check' => 'tuple',
                    'command' => 'brew install stoplight-studio',
                ],

                'tuple' => [
                    'name' => 'Tuple',
                    'url' => 'https://tuple.app/',
                    'licensed' => false,
                    'check' => 'tuple',
                    'command' => 'brew install tuple',
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
