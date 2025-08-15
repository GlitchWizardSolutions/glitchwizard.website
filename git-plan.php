<?php
/*
 * GWS UNIVERSAL HYBRID APP - COMPLETE GIT VERSION CONTROL SETUP PLAN
 * 
 * PURPOSE: Step-by-step beginner's guide to Git for XAMPP to cPanel workflow
 * TARGET AUDIENCE: Complete beginners (5th grade reading level)
 * CREATED: August 9, 2025
 * VERSION: 1.0 - Complete Beginner Setup
 * 
 * WHY YOU NEED THIS:
 * 1. Backup and safety for canonical-plan.php execution
 * 2. Template management for multiple implementations
 * 3. XAMPP development to cPanel production deployment
 * 4. Change tracking and rollback capability
 * 5. Professional development workflow
 * 
 * THIS PLAN COVERS:
 * - Installing Git on Windows
 * - Setting up your first repository
 * - Basic Git commands with examples
 * - XAMPP to cPanel workflow
 * - Template distribution to multiple sites
 * - Emergency rollback procedures
 */

// ============================================================================
// SECTION: STEP-BY-STEP GITHUB TO CPANEL DEPLOYMENT WORKFLOW
// ============================================================================
/*
 * 1. Create a Private GitHub Repository
 *    - Go to https://github.com/ and log in.
 *    - Click the "+" icon → "New repository".
 *    - Name your repo (e.g., gws-universal-hybrid-app), set it to Private, and click "Create repository".
 *
 * 2. Initialize Git Locally (if not already done)
 *    cd C:\xampp\htdocs\gws-universal-hybrid-app\public_html
 *    git init
 *    git add .
 *    git commit -m "Initial commit"
 *    git branch -M main
 *
 * 3. Connect Your Local Repo to GitHub
 *    - Copy the HTTPS URL from your new GitHub repo (e.g., https://github.com/yourusername/gws-universal-hybrid-app.git).
 *    git remote add origin https://github.com/yourusername/gws-universal-hybrid-app.git
 *    git push -u origin main
 *    - If prompted, log in with your GitHub username and a personal access token.
 *
 * 4. Set Up cPanel to Pull from GitHub
 *    - In cPanel, go to Git Version Control.
 *    - Click Create or Create Repository.
 *    - Enable Clone a Repository.
 *    - Paste your GitHub repo’s URL.
 *    - Set the Repository Path to /home/youruser/public_html.
 *    - Click Create.
 *
 * 5. Deploy and Update
 *    - When you push new changes to GitHub, go to cPanel’s Git Version Control and click Pull to update your live site.
 *
 * TROUBLESHOOTING & TIPS
 * - Always push to GitHub first, then pull from cPanel.
 * - Use the main branch for all pushes and pulls.
 * - If you get authentication errors, use a GitHub personal access token.
 * - If cPanel can’t pull, check your repo privacy and deploy key/authorization settings.
 */

// ============================================================================
// SECTION: QUICK START – LOCAL TO CPANEL GIT WORKFLOW
// ============================================================================
/*
 * 1. Open your terminal (VS Code Terminal or Windows Command Prompt).
 * 2. Navigate to your local public_html directory:
 *      cd C:\xampp\htdocs\gws-universal-hybrid-app\public_html
 * 3. Initialize Git (if not already done):
 *      git init
 * 4. Add all files:
 *      git add .
 * 5. Commit your changes:
 *      git commit -m "Initial commit"
 * 6. Set your branch name to main (if not already main):
 *      git branch -M main
 * 7. Add your cPanel remote (replace with your actual HTTPS or SSH URL):
 *      git remote add production https://yourdomain.com/git_repository_public
 * 8. Verify the remote:
 *      git remote -v
 * 9. Push your code to cPanel:
 *      git push -u production main
 *      (You may be prompted for your cPanel username and password.)
 * 10. In cPanel, use Git Version Control to deploy the latest commit if needed.
 *
 * TROUBLESHOOTING:
 * - If you get errors with 'main', run 'git branch' to check your branch name. If it's not main, use 'git branch -M main' to rename it.
 * - If 'git add .' gives errors, check file permissions and that no files are open in other programs.
 * - Always run Git commands from the public_html directory to match your cPanel repo structure.
 * - Use the same branch name ('main') for all examples and pushes.
 */

// ============================================================================
// SECTION 1: WHY GIT IS ESSENTIAL FOR YOUR WORKFLOW
// ============================================================================

$why_git_is_essential = [
    'before_canonical_plan_execution' => [
        'safety_backup' => 'Git creates automatic backups before canonical-plan.php makes changes',
        'rollback_capability' => 'If anything goes wrong, you can instantly restore to working state',
        'change_tracking' => 'See exactly what canonical-plan.php changed, line by line',
        'multiple_save_points' => 'Create save points before each phase of cleanup'
    ],
    'for_template_distribution' => [
        'master_template' => 'Your XAMPP workspace becomes the "master template"',
        'copy_to_multiple_sites' => 'Easy distribution to multiple cPanel accounts',
        'sync_changes' => 'Update all sites when you improve the template',
        'track_customizations' => 'See what\'s different between sites'
    ],
    'xampp_to_cpanel_workflow' => [
        'develop_locally' => 'Work safely in XAMPP without affecting live sites',
        'test_thoroughly' => 'Test all changes before pushing to production',
        'deploy_confidently' => 'Push tested changes to live cPanel sites',
        'emergency_rollback' => 'Instantly revert live sites if problems occur'
    ],
    'professional_benefits' => [
        'industry_standard' => 'Git is used by 99% of professional developers',
        'collaboration_ready' => 'Others can help you without breaking things',
        'change_history' => 'Never lose work or wonder "what did I change?"',
        'branching' => 'Try experimental features without risk'
    ]
];

// ============================================================================
// SECTION 2: VS CODE + GIT INTEGRATION SETUP (ONE-TIME SETUP)
// ============================================================================

$vscode_git_integration = [
    'why_vscode_with_git' => [
        'visual_interface' => 'See changes highlighted in your code editor',
        'built_in_git_commands' => 'No need to memorize terminal commands',
        'commit_from_editor' => 'Save versions directly from where you code',
        'branch_management' => 'Switch between different versions visually',
        'merge_conflict_resolution' => 'VS Code helps resolve conflicts automatically'
    ],
    'step_1_install_vscode_extensions' => [
        'action' => 'Install essential Git extensions for VS Code',
        'instructions' => [
            '1' => 'Open VS Code',
            '2' => 'Press Ctrl+Shift+X to open Extensions panel',
            '3' => 'Search for "GitLens" and click Install',
            '4' => 'Search for "Git Graph" and click Install',
            '5' => 'Search for "Git History" and click Install',
            '6' => 'Search for "GitHub Pull Requests" and click Install (for future GitHub integration)',
            '7' => 'Restart VS Code when prompted'
        ],
        'what_each_extension_does' => [
            'GitLens' => 'Shows who changed what code and when (like detective work)',
            'Git Graph' => 'Visual timeline of all your changes',
            'Git History' => 'Browse through old versions of files',
            'GitHub Pull Requests' => 'For sharing code and collaboration'
        ]
    ],
    'step_2_configure_vscode_git_settings' => [
        'action' => 'Set up VS Code to work perfectly with Git',
        'instructions' => [
            '1' => 'Press Ctrl+, to open VS Code settings',
            '2' => 'Search for "git.autofetch" and check the box',
            '3' => 'Search for "git.confirmSync" and uncheck the box',
            '4' => 'Search for "git.enableSmartCommit" and check the box',
            '5' => 'Search for "git.postCommitCommand" and set to "sync"',
            '6' => 'Search for "terminal.integrated.defaultProfile.windows" and set to "Command Prompt"'
        ],
        'what_these_settings_do' => [
            'autofetch' => 'Automatically checks for updates from your cPanel sites',
            'confirmSync' => 'Removes annoying confirmation dialogs',
            'enableSmartCommit' => 'Automatically stages files when you commit',
            'postCommitCommand' => 'Automatically syncs with remote servers after commits'
        ]
    ],
    'step_3_open_workspace_in_vscode' => [
        'action' => 'Open your GWS workspace in VS Code with Git integration',
        'instructions' => [
            '1' => 'Open VS Code',
            '2' => 'Press Ctrl+K, then Ctrl+O to open folder',
            '3' => 'Navigate to C:\\xampp\\htdocs\\gws-universal-hybrid-app',
            '4' => 'Click "Select Folder"',
            '5' => 'VS Code will detect the Git repository automatically',
            '6' => 'You should see a source control icon on the left sidebar with a number'
        ],
        'success_indicators' => [
            'git_icon_visible' => 'Source control icon appears in left sidebar',
            'branch_name_shown' => 'Bottom left shows "main" or current branch name',
            'file_colors' => 'Modified files show in different colors in explorer'
        ]
    ],
    'step_4_using_git_in_vscode' => [
        'basic_workflow' => [
            'make_changes' => 'Edit files normally in VS Code',
            'see_changes' => 'Click Source Control icon (Ctrl+Shift+G) to see what changed',
            'stage_changes' => 'Click + next to files you want to save',
            'write_message' => 'Type commit message in the text box',
            'commit_changes' => 'Click checkmark or press Ctrl+Enter to save',
            'push_to_server' => 'Click ... menu and select "Push" to send to cPanel'
        ],
        'visual_indicators' => [
            'green_files' => 'New files (untracked)',
            'yellow_files' => 'Modified files',
            'red_files' => 'Deleted files',
            'blue_M' => 'Modified and staged for commit'
        ]
    ]
];

// ============================================================================
// SECTION 3: ABSOLUTE BEGINNER SETUP - WINDOWS INSTALLATION
// ============================================================================

$windows_git_installation = [
    'step_1_download_git' => [
        'action' => 'Download Git for Windows',
        'instructions' => [
            '1' => 'Open your web browser (Chrome, Firefox, Edge)',
            '2' => 'Go to: https://git-scm.com/download/win',
            '3' => 'Click the blue "Download" button',
            '4' => 'Wait for download to complete (usually 2-3 minutes)',
            '5' => 'File will be named something like "Git-2.41.0-64-bit.exe"'
        ],
        'what_youre_downloading' => 'Git software + Git Bash (command line) + Git GUI (visual interface)',
        'file_size' => 'About 50MB - normal download time on decent internet'
    ],
    'step_2_install_git' => [
        'action' => 'Install Git with correct settings',
        'instructions' => [
            '1' => 'Find the downloaded file (usually in Downloads folder)',
            '2' => 'Double-click the Git installer file',
            '3' => 'Click "Yes" if Windows asks for permission',
            '4' => 'Click "Next" for the welcome screen',
            '5' => 'Choose install location - default is fine: C:\\Program Files\\Git',
            '6' => 'Click "Next" for components - default selections are perfect',
            '7' => 'Click "Next" for Start Menu folder - default is fine',
            '8' => 'IMPORTANT: Default editor - choose "Use Visual Studio Code" if you have it',
            '9' => 'IMPORTANT: Initial branch name - choose "Override" and type "main"',
            '10' => 'IMPORTANT: PATH environment - choose "Git from the command line and also from 3rd-party software"',
            '11' => 'IMPORTANT: HTTPS transport - choose "Use the OpenSSL library"',
            '12' => 'IMPORTANT: Line ending conversions - choose "Checkout Windows-style, commit Unix-style"',
            '13' => 'IMPORTANT: Terminal emulator - choose "Use MinTTY"',
            '14' => 'IMPORTANT: git pull behavior - choose "Default (fast-forward or merge)"',
            '15' => 'IMPORTANT: Credential helper - choose "Git Credential Manager"',
            '16' => 'Extra options - check "Enable file system caching" only',
            '17' => 'Click "Install" and wait (usually 1-2 minutes)',
            '18' => 'Click "Finish" when done'
        ],
        'critical_settings' => [
            'editor' => 'Visual Studio Code (if you have it) or Notepad++',
            'branch_name' => 'main (not master)',
            'path_setting' => 'Command line + 3rd party software',
            'line_endings' => 'Windows-style checkout, Unix-style commit'
        ]
    ],
    'step_3_verify_installation' => [
        'action' => 'Test that Git is working',
        'instructions' => [
            '1' => 'Press Windows key + R to open Run dialog',
            '2' => 'Type "cmd" and press Enter (opens Command Prompt)',
            '3' => 'Type: git --version',
            '4' => 'Press Enter',
            '5' => 'You should see something like: git version 2.41.0.windows.1',
            '6' => 'If you see a version number, Git is installed correctly!',
            '7' => 'If you see "git is not recognized", installation failed - try again'
        ],
        'success_indicator' => 'You see "git version" followed by numbers',
        'failure_indicator' => 'You see "git is not recognized as an internal or external command"'
    ]
];

// ============================================================================
// SECTION 3: FIRST-TIME GIT CONFIGURATION (ONE-TIME SETUP)
// ============================================================================

$first_time_configuration = [
    'step_1_set_your_identity' => [
        'why_needed' => 'Git needs to know who you are for tracking changes',
        'action' => 'Tell Git your name and email',
        'instructions' => [
            '1' => 'Open Command Prompt (Windows key + R, type "cmd", press Enter)',
            '2' => 'Type: git config --global user.name "Your Full Name"',
            '3' => 'Press Enter',
            '4' => 'Type: git config --global user.email "your.email@example.com"',
            '5' => 'Press Enter',
            '6' => 'Type: git config --global init.defaultBranch main',
            '7' => 'Press Enter'
        ],
        'example' => [
            'name_command' => 'git config --global user.name "John Smith"',
            'email_command' => 'git config --global user.email "john@mycompany.com"',
            'branch_command' => 'git config --global init.defaultBranch main'
        ],
        'important_notes' => [
            'use_real_name' => 'Use your actual name - this appears in change history',
            'use_real_email' => 'Use your real email - needed for some Git services',
            'quotes_required' => 'Keep the quotes around your name and email'
        ]
    ],
    'step_2_verify_configuration' => [
        'action' => 'Check that Git remembers your settings',
        'instructions' => [
            '1' => 'In Command Prompt, type: git config --list',
            '2' => 'Press Enter',
            '3' => 'Look for lines like:',
            '4' => '  user.name=Your Full Name',
            '5' => '  user.email=your.email@example.com',
            '6' => '  init.defaultbranch=main',
            '7' => 'If you see these, configuration is correct!'
        ],
        'success_indicator' => 'You see your name and email in the list',
        'what_if_wrong' => 'Run the setup commands again with correct information'
    ]
];

// ============================================================================
// SECTION 4: CREATING YOUR FIRST REPOSITORY (FOR GWS UNIVERSAL HYBRID APP)
// ============================================================================

$first_repository_setup = [
    'step_1_navigate_to_project' => [
        'action' => 'Go to your GWS Universal Hybrid App folder',
        'instructions' => [
            '1' => 'Open Command Prompt',
            '2' => 'Type: cd c:\\xampp\\htdocs\\gws-universal-hybrid-app',
            '3' => 'Press Enter',
            '4' => 'You should see the prompt change to show this folder path',
            '5' => 'Type: dir',
            '6' => 'Press Enter',
            '7' => 'You should see folders like public_html, private, assets, etc.'
        ],
        'what_cd_means' => 'cd = Change Directory (move to a different folder)',
        'what_dir_means' => 'dir = Directory listing (show what\'s in current folder)',
        'success_indicator' => 'You see your project folders listed'
    ],
    'step_2_initialize_git_repository' => [
        'action' => 'Turn your project folder into a Git repository',
        'instructions' => [
            '1' => 'Make sure you\'re in c:\\xampp\\htdocs\\gws-universal-hybrid-app',
            '2' => 'Type: git init',
            '3' => 'Press Enter',
            '4' => 'You should see: "Initialized empty Git repository in..."',
            '5' => 'This creates a hidden .git folder that tracks changes'
        ],
        'what_git_init_does' => [
            'creates_tracking' => 'Creates .git folder to track all changes',
            'enables_commands' => 'Enables all Git commands in this folder',
            'starts_history' => 'Begins version history tracking'
        ],
        'success_indicator' => 'You see "Initialized empty Git repository"'
    ],
    'step_3_create_gitignore_file' => [
        'action' => 'Tell Git what files to ignore',
        'why_needed' => 'Some files shouldn\'t be tracked (passwords, temporary files, etc.)',
        'instructions' => [
            '1' => 'Type: notepad .gitignore',
            '2' => 'Press Enter',
            '3' => 'Notepad will ask "Create new file?" - click Yes',
            '4' => 'Copy and paste this content into Notepad:',
            '5' => '# Ignore sensitive files',
            '6' => '/private/openai-key.php',
            '7' => '/private/*-key.php',
            '8' => '*.log',
            '9' => 'tmp/',
            '10' => 'temp/',
            '11' => '.DS_Store',
            '12' => 'Thumbs.db',
            '13' => 'Save the file (Ctrl+S) and close Notepad'
        ],
        'what_gitignore_does' => 'Prevents sensitive files from being tracked or shared',
        'critical_protection' => 'Keeps API keys and passwords out of version control'
    ],
    'step_4_add_all_files' => [
        'action' => 'Add all your project files to Git tracking',
        'instructions' => [
            '1' => 'Type: git add .',
            '2' => 'Press Enter',
            '3' => 'This adds all files to "staging area" (ready to save)',
            '4' => 'Type: git status',
            '5' => 'Press Enter',
            '6' => 'You\'ll see a long list of "new file:" entries in green'
        ],
        'what_git_add_does' => 'Prepares files to be saved in version history',
        'what_the_dot_means' => '. means "all files in current folder and subfolders"',
        'success_indicator' => 'git status shows many green "new file:" entries'
    ],
    'step_5_first_commit' => [
        'action' => 'Save your first version snapshot',
        'instructions' => [
            '1' => 'Type: git commit -m "Initial commit - GWS Universal Hybrid App baseline"',
            '2' => 'Press Enter',
            '3' => 'You\'ll see output showing files were saved',
            '4' => 'Type: git log --oneline',
            '5' => 'Press Enter',
            '6' => 'You\'ll see your first commit listed'
        ],
        'what_commit_means' => 'Saves a snapshot you can return to later',
        'what_the_message_is' => 'Description of what this version contains',
        'success_indicator' => 'You see confirmation and your commit in git log'
    ]
];

// ============================================================================
// SECTION 5: ESSENTIAL GIT COMMANDS FOR DAILY USE
// ============================================================================

$essential_daily_commands = [
    'checking_status' => [
        'command' => 'git status',
        'what_it_does' => 'Shows what files have changed since last save',
        'when_to_use' => 'Use this constantly - before and after making changes',
        'example_output' => [
            'clean_working_tree' => 'nothing to commit, working tree clean (no changes)',
            'modified_files' => 'modified: public_html/canonical-plan.php (file was changed)',
            'new_files' => 'untracked files: new-feature.php (new file created)'
        ]
    ],
    'saving_changes' => [
        'step_1' => 'git add . (add all changed files to staging)',
        'step_2' => 'git commit -m "Describe what you changed" (save the snapshot)',
        'examples' => [
            'git commit -m "Added user login feature"',
            'git commit -m "Fixed contact form validation bug"',
            'git commit -m "Updated CSS for mobile responsiveness"'
        ],
        'message_tips' => [
            'be_descriptive' => 'Explain what you did, not how you did it',
            'use_present_tense' => '"Add feature" not "Added feature"',
            'keep_under_50_chars' => 'Short but informative'
        ]
    ],
    'viewing_history' => [
        'command' => 'git log --oneline',
        'what_it_shows' => 'List of all saved versions, newest first',
        'example_output' => [
            'a1b2c3d Updated contact form validation',
            'e4f5g6h Added user registration system', 
            'i7j8k9l Initial commit - baseline'
        ],
        'reading_the_output' => [
            'a1b2c3d' => 'Unique ID for this version (commit hash)',
            'Updated contact form' => 'Message you wrote when saving'
        ]
    ],
    'going_back_in_time' => [
        'view_old_version' => 'git checkout a1b2c3d (view files as they were)',
        'return_to_present' => 'git checkout main (return to current version)',
        'permanent_rollback' => 'git reset --hard a1b2c3d (WARNING: destroys newer changes)',
        'safety_warning' => 'Always commit current changes before going back in time'
    ]
];

// ============================================================================
// SECTION 6: BACKUP STRATEGY BEFORE CANONICAL-PLAN.PHP EXECUTION
// ============================================================================

$backup_strategy_for_canonical_plan = [
    'step_1_create_clean_baseline' => [
        'action' => 'Ensure current state is saved as safe baseline',
        'instructions' => [
            '1' => 'Navigate to project: cd c:\\xampp\\htdocs\\gws-universal-hybrid-app',
            '2' => 'Check status: git status',
            '3' => 'If any files are modified, save them: git add . && git commit -m "Pre-cleanup baseline"',
            '4' => 'Verify clean state: git status (should say "working tree clean")',
            '5' => 'Create backup tag: git tag baseline-before-cleanup',
            '6' => 'Verify tag created: git tag --list'
        ],
        'what_tagging_does' => 'Creates a permanent bookmark you can always return to',
        'success_indicator' => 'git status shows "working tree clean" and tag appears in list'
    ],
    'step_2_pre_execution_safety_commit' => [
        'action' => 'Create save point immediately before running canonical-plan.php',
        'instructions' => [
            '1' => 'Add any final changes: git add .',
            '2' => 'Commit with clear message: git commit -m "Ready for canonical-plan.php execution - all tests pass"',
            '3' => 'Create safety tag: git tag ready-for-cleanup',
            '4' => 'Take screenshot of working admin pages',
            '5' => 'Document current admin page load times'
        ],
        'why_this_matters' => 'If canonical-plan.php breaks anything, you can instantly return here'
    ],
    'step_3_during_execution_checkpoints' => [
        'action' => 'Save progress after each major phase',
        'checkpoint_commits' => [
            'after_dependency_mapping' => 'git commit -m "Checkpoint: dependency mapping complete"',
            'after_file_quarantine' => 'git commit -m "Checkpoint: orphaned files quarantined"',
            'after_style_extraction' => 'git commit -m "Checkpoint: inline styles extracted"',
            'after_cleanup_complete' => 'git commit -m "Cleanup complete - all tests passing"'
        ],
        'tagging_major_milestones' => [
            'git tag cleanup-phase-1-complete',
            'git tag cleanup-phase-2-complete',
            'git tag cleanup-finished'
        ]
    ],
    'step_4_emergency_rollback_procedure' => [
        'if_something_breaks' => [
            'immediate_action' => 'STOP canonical-plan.php execution',
            'assess_damage' => 'Load admin pages and test functionality',
            'find_last_good_version' => 'git log --oneline (find last working commit)',
            'rollback_command' => 'git reset --hard [commit-id]',
            'example' => 'git reset --hard ready-for-cleanup'
        ],
        'verification_after_rollback' => [
            '1' => 'Test all admin pages work correctly',
            '2' => 'Verify database connections still work',
            '3' => 'Check that all CSS/JS loads properly',
            '4' => 'Confirm canonical-plan.php execution can be restarted'
        ]
    ]
];

// ============================================================================
// SECTION 7: RIGGSZOO.COM TEMPLATE SETUP (INITIAL DEMO SITE)
// ============================================================================

$riggszoo_template_setup = [
    'overview' => [
        'purpose' => 'Set up riggszoo.com as your master template demo site',
        'workflow' => 'XAMPP development → riggszoo.com deployment → share with clients',
        'benefits' => [
            'live_demo' => 'Clients can see working template before purchasing',
            'testing_ground' => 'Test new features on live environment',
            'template_distribution' => 'Source for all future client sites',
            'professional_presentation' => 'Shows you have working systems'
        ]
    ],
    'step_1_cpanel_preparation_riggszoo' => [
        'action' => 'Prepare riggszoo.com cPanel for Git integration',
        'instructions' => [
            '1' => 'Log into riggszoo.com cPanel',
            '2' => 'Navigate to "File Manager"',
            '3' => 'Go to public_html directory',
            '4' => 'Select all existing files (if any) and move to backup folder',
            '5' => 'Create backup folder: "backup-original-files-' . date('Y-m-d') . '"',
            '6' => 'Return to public_html (should now be empty)',
            '7' => 'Look for "Git Version Control" in cPanel main menu'
        ],
        'cpanel_git_location' => [
            'common_names' => '"Git Version Control", "Git™ Version Control", "Version Control"',
            'if_not_found' => 'Contact hosting provider - not all hosts support Git',
            'alternative' => 'Use SFTP/FTP deployment if Git not available'
        ]
    ],
    'step_2_initialize_git_on_riggszoo' => [
        'action' => 'Set up Git repository on riggszoo.com',
        'method_a_cpanel_git' => [
            'if_cpanel_has_git' => [
                '1' => 'Click "Git Version Control" in cPanel',
                '2' => 'Click "Create Repository"',
                '3' => 'Repository Path: /public_html',
                '4' => 'Repository Name: gws-template',
                '5' => 'Click "Create" button',
                '6' => 'Note the clone URL provided (save this!)'
            ]
        ],
        'method_b_ssh_terminal' => [
            'if_ssh_access_available' => [
                '1' => 'Use cPanel Terminal or SSH client',
                '2' => 'cd public_html',
                '3' => 'git init',
                '4' => 'git config user.name "Your Name"',
                '5' => 'git config user.email "your@email.com"',
                '6' => 'git checkout -b main'
            ]
        ],
        'save_connection_info' => [
            'clone_url_example' => 'username@riggszoo.com:public_html/gws-template.git',
            'ssh_format' => 'ssh://username@riggszoo.com:2222/home/username/public_html',
            'https_format' => 'https://riggszoo.com/git/gws-template.git'
        ]
    ],
    'step_3_connect_xampp_to_riggszoo' => [
        'action' => 'Link your XAMPP workspace to riggszoo.com',
        'instructions' => [
            '1' => 'Open VS Code with your GWS workspace',
            '2' => 'Open integrated terminal (Ctrl+` backtick)',
            '3' => 'Ensure you\'re in the right folder: pwd or cd',
            '4' => 'Add riggszoo as remote: git remote add riggszoo [clone-url-from-step-2]',
            '5' => 'Verify connection: git remote -v',
            '6' => 'You should see both "origin" and "riggszoo" listed'
        ],
        'example_commands' => [
            'git remote add riggszoo ssh://username@riggszoo.com:2222/home/username/public_html',
            'git remote add riggszoo https://username:password@riggszoo.com/git/gws-template.git'
        ]
    ],
    'step_4_initial_deployment_to_riggszoo' => [
        'action' => 'Deploy your clean template to riggszoo.com',
        'pre_deployment_checklist' => [
            '1' => 'Canonical-plan.php cleanup completed successfully',
            '2' => 'All admin functionality tested in XAMPP',
            '3' => 'Database configuration updated for riggszoo.com',
            '4' => 'Email settings configured for riggszoo.com domain',
            '5' => 'All file paths are relative (not XAMPP-specific)',
            '6' => 'Sensitive files (.htaccess, config files) updated'
        ],
        'deployment_steps' => [
            '1' => 'Create deployment tag: git tag riggszoo-v1.0',
            '2' => 'Push to riggszoo: git push riggszoo main',
            '3' => 'If prompted for credentials, use cPanel username/password',
            '4' => 'Monitor terminal for success/error messages',
            '5' => 'SSH into riggszoo.com (or use cPanel terminal)',
            '6' => 'cd public_html && git status (verify files are there)',
            '7' => 'Visit riggszoo.com in browser to test'
        ]
    ],
    'step_5_riggszoo_database_setup' => [
        'action' => 'Set up database for riggszoo.com template',
        'instructions' => [
            '1' => 'In riggszoo.com cPanel, go to "MySQL Databases"',
            '2' => 'Create new database: riggszoo_template',
            '3' => 'Create database user with full privileges',
            '4' => 'Export your XAMPP database (phpMyAdmin → Export)',
            '5' => 'Import to riggszoo database (cPanel phpMyAdmin → Import)',
            '6' => 'Update database config in /private/gws-universal-config.php',
            '7' => 'Test database connection by loading admin area'
        ],
        'config_file_updates' => [
            'database_host' => 'Usually "localhost" but check with hosting provider',
            'database_name' => 'riggszoo_template (or whatever you created)',
            'database_user' => 'Your database username',
            'database_password' => 'Your database password'
        ]
    ],
    'step_6_riggszoo_testing_and_verification' => [
        'action' => 'Thoroughly test riggszoo.com deployment',
        'testing_checklist' => [
            'frontend_tests' => [
                '1' => 'Visit riggszoo.com - homepage loads correctly',
                '2' => 'Navigation menus work',
                '3' => 'Contact forms submit successfully',
                '4' => 'Blog pages display properly',
                '5' => 'All images and assets load'
            ],
            'admin_tests' => [
                '1' => 'Admin login works (riggszoo.com/admin/)',
                '2' => 'Dashboard displays correctly',
                '3' => 'All admin functions work',
                '4' => 'Database connections functional',
                '5' => 'File uploads work properly'
            ],
            'accounts_system_tests' => [
                '1' => 'User registration works',
                '2' => 'Email notifications send',
                '3' => 'Login/logout functions',
                '4' => 'Password reset works'
            ]
        ]
    ]
];

// ============================================================================
// SECTION 8: STREAMLINED FUTURE WORKSPACE SETUP
// ============================================================================

$future_workspace_setup = [
    'overview' => [
        'assumption' => 'VS Code and Git are already installed and configured',
        'goal' => 'Quickly set up new workspace → new cPanel site connections',
        'time_estimate' => '15-30 minutes per new site (after first one)',
        'workflow' => 'Clone template → customize → deploy → maintain'
    ],
    'step_1_create_new_workspace_from_template' => [
        'action' => 'Create new client workspace from riggszoo.com template',
        'instructions' => [
            '1' => 'Open Command Prompt or VS Code terminal',
            '2' => 'Navigate to XAMPP directory: cd C:\\xampp\\htdocs',
            '3' => 'Clone from riggszoo: git clone riggszoo_repo_url client-site-name',
            '4' => 'Alternative: Copy existing workspace: xcopy /E /I gws-universal-hybrid-app client-abc-company',
            '5' => 'Navigate to new folder: cd client-abc-company',
            '6' => 'Initialize as new repository: git init',
            '7' => 'Add all files: git add .',
            '8' => 'Initial commit: git commit -m "Initial setup from riggszoo template"'
        ],
        'naming_conventions' => [
            'client-company-name' => 'client-abc-widgets',
            'client-domain-name' => 'client-abcwidgets-com',
            'descriptive-purpose' => 'client-law-firm-smith'
        ]
    ],
    'step_2_customize_for_new_client' => [
        'action' => 'Customize template for specific client needs',
        'customization_areas' => [
            'branding' => [
                'logo' => 'Replace logo files in /assets/img/',
                'colors' => 'Update CSS color variables',
                'fonts' => 'Change font selections if needed',
                'favicon' => 'Replace favicon.ico'
            ],
            'content' => [
                'homepage' => 'Update homepage content and images',
                'about_page' => 'Customize about page content',
                'contact_info' => 'Update contact forms and information',
                'services' => 'Modify service offerings'
            ],
            'configuration' => [
                'site_title' => 'Update in universal config',
                'meta_descriptions' => 'SEO settings for new domain',
                'email_settings' => 'SMTP for new domain',
                'database_prefix' => 'Use client-specific table prefixes'
            ]
        ],
        'commit_strategy' => [
            'frequent_commits' => 'Commit after each major customization',
            'clear_messages' => '"Added ABC Company branding and logo"',
            'feature_branches' => 'Use branches for experimental features'
        ]
    ],
    'step_3_setup_new_cpanel_site' => [
        'action' => 'Prepare new client\'s cPanel for deployment',
        'cpanel_preparation' => [
            '1' => 'Log into client\'s cPanel (clientdomain.com/cpanel)',
            '2' => 'Create database: clientname_main',
            '3' => 'Create database user with full privileges',
            '4' => 'Set up email accounts if needed',
            '5' => 'Configure SSL certificate',
            '6' => 'Set up Git repository (if supported)',
            '7' => 'Configure backup schedule'
        ],
        'git_setup_on_new_cpanel' => [
            'if_git_supported' => [
                '1' => 'cPanel → Git Version Control',
                '2' => 'Create Repository in public_html',
                '3' => 'Note clone URL for connection'
            ],
            'if_no_git_support' => [
                '1' => 'Use SFTP/FTP for file transfer',
                '2' => 'Set up automated backup system',
                '3' => 'Consider upgrading hosting plan'
            ]
        ]
    ],
    'step_4_connect_workspace_to_new_cpanel' => [
        'action' => 'Link new workspace to new cPanel site',
        'connection_steps' => [
            '1' => 'In VS Code, open new client workspace',
            '2' => 'Open terminal (Ctrl+`)',
            '3' => 'Add client cPanel as remote: git remote add production client_cpanel_url',
            '4' => 'Verify connection: git remote -v',
            '5' => 'Test connection: git ls-remote production',
            '6' => 'If successful, you\'re ready to deploy'
        ],
        'multiple_remotes_strategy' => [
            'template_remote' => 'riggszoo (for getting template updates)',
            'production_remote' => 'production (client\'s live site)',
            'staging_remote' => 'staging (client\'s test site, if available)'
        ]
    ],
    'step_5_deployment_workflow' => [
        'action' => 'Deploy customized site to client cPanel',
        'deployment_process' => [
            '1' => 'Final testing in XAMPP',
            '2' => 'Update database config for production',
            '3' => 'Create deployment tag: git tag client-launch-v1.0',
            '4' => 'Push to production: git push production main',
            '5' => 'SSH into client cPanel or use terminal',
            '6' => 'Import database from XAMPP export',
            '7' => 'Test all functionality on live site',
            '8' => 'Configure domain DNS if needed'
        ]
    ],
    'step_6_ongoing_maintenance_workflow' => [
        'action' => 'Maintain multiple client sites efficiently',
        'workflow_overview' => [
            'template_updates' => 'Update riggszoo.com template with new features',
            'merge_to_clients' => 'Selectively merge updates to client sites',
            'client_customizations' => 'Maintain client-specific modifications',
            'coordinated_deployments' => 'Update multiple sites systematically'
        ],
        'daily_operations' => [
            'development' => 'Work in appropriate XAMPP workspace',
            'testing' => 'Test locally before any deployment',
            'staging' => 'Deploy to staging if available',
            'production' => 'Deploy to live site after approval',
            'monitoring' => 'Monitor live sites for issues'
        ]
    ]
];

// ============================================================================
// SECTION 9: MULTI-SITE MANAGEMENT BEST PRACTICES
// ============================================================================

$multi_site_management = [
    'workspace_organization' => [
        'folder_structure' => [
            'C:\\xampp\\htdocs\\gws-universal-hybrid-app' => 'Master template development',
            'C:\\xampp\\htdocs\\client-abc-company' => 'ABC Company customizations',
            'C:\\xampp\\htdocs\\client-xyz-law-firm' => 'XYZ Law Firm customizations',
            'C:\\xampp\\htdocs\\riggszoo-demo' => 'Riggszoo demo site sync'
        ],
        'vs_code_workspaces' => [
            'create_workspace_files' => 'Save multi-folder workspaces in VS Code',
            'switch_between_projects' => 'Use Ctrl+R to switch workspaces quickly',
            'terminal_per_project' => 'Each workspace gets its own terminal'
        ]
    ],
    'git_remote_management' => [
        'naming_conventions' => [
            'template' => 'Remote pointing to riggszoo.com template',
            'production' => 'Client\'s live cPanel site',
            'staging' => 'Client\'s staging site (if available)',
            'backup' => 'Backup repository location'
        ],
        'example_remote_setup' => [
            'git remote add template ssh://user@riggszoo.com/public_html',
            'git remote add production ssh://user@clientsite.com/public_html',
            'git remote add staging ssh://user@staging.clientsite.com/public_html'
        ]
    ],
    'update_propagation_strategy' => [
        'template_improvements' => [
            '1' => 'Develop new features in master template',
            '2' => 'Test thoroughly in riggszoo.com',
            '3' => 'Tag stable version: git tag template-v2.0',
            '4' => 'Selectively merge to client sites',
            '5' => 'Test each client site individually',
            '6' => 'Deploy to production after approval'
        ],
        'selective_merging' => [
            'feature_branches' => 'Develop features in separate branches',
            'cherry_picking' => 'Select specific improvements for specific clients',
            'customization_preservation' => 'Never overwrite client customizations'
        ]
    ]
];

// ============================================================================
// SECTION 10: TROUBLESHOOTING MULTI-SITE WORKFLOWS
// ============================================================================

// Adding new sections for completeness

$merge_conflict_management = [
    'basic_conflict_resolution' => [
        'understanding_conflicts' => [
            'what_is_conflict' => 'When Git cannot automatically merge changes',
            'conflict_markers' => [
                '<<<<<<< HEAD' => 'Your local changes start here',
                '=======' => 'Separator between versions',
                '>>>>>>> branch-name' => 'Incoming changes end here'
            ]
        ],
        'resolution_steps' => [
            '1' => 'Open conflicted file in VS Code',
            '2' => 'Look for conflict markers (<<<<<<, =======, >>>>>>>)',
            '3' => 'Compare both versions of the code',
            '4' => 'Choose which version to keep or combine them',
            '5' => 'Remove conflict markers',
            '6' => 'Save the file',
            '7' => 'git add [filename]',
            '8' => 'git commit -m "Resolve merge conflict in [filename]"'
        ],
        'example_conflict' => [
            'scenario' => 'Two developers changed the same function',
            'local_version' => 'function getUser() { return $user->id; }',
            'remote_version' => 'function getUser() { return $user->name; }',
            'resolution' => 'function getUser() { return ["id" => $user->id, "name" => $user->name]; }'
        ]
    ],
    'prevention_strategies' => [
        'communication' => [
            'team_coordination' => 'Discuss who is working on what files',
            'regular_updates' => 'Pull from main branch frequently',
            'small_commits' => 'Make smaller, focused changes'
        ],
        'code_organization' => [
            'modular_code' => 'Split functionality into separate files',
            'clear_ownership' => 'Assign clear ownership of code sections',
            'documentation' => 'Comment code thoroughly'
        ]
    ]
];

$branching_strategies = [
    'feature_branches' => [
        'purpose' => 'Isolate new feature development from main code',
        'workflow' => [
            '1' => 'Create branch: git checkout -b feature/new-contact-form',
            '2' => 'Make changes and commit regularly',
            '3' => 'Push branch: git push origin feature/new-contact-form',
            '4' => 'Create pull request when ready',
            '5' => 'Merge after review and testing'
        ],
        'naming_conventions' => [
            'feature/*' => 'For new features (feature/user-registration)',
            'bugfix/*' => 'For bug fixes (bugfix/login-error)',
            'hotfix/*' => 'For urgent production fixes'
        ]
    ],
    'release_branches' => [
        'purpose' => 'Prepare and stabilize code for release',
        'workflow' => [
            '1' => 'Create from main: git checkout -b release/1.0',
            '2' => 'Fix bugs and prepare release notes',
            '3' => 'No new features in release branches',
            '4' => 'Merge to main and tag when ready'
        ]
    ]
];

$database_version_control = [
    'migration_strategy' => [
        'principles' => [
            'version_tracking' => 'Track database schema changes in Git',
            'reversible_changes' => 'All changes should have rollback options',
            'incremental_updates' => 'Small, sequential changes preferred'
        ],
        'implementation' => [
            'sql_files' => [
                'location' => '/private/sql/migrations/',
                'naming' => 'YYYY-MM-DD_description.sql',
                'content' => 'Both UP and DOWN migrations'
            ],
            'tracking_table' => [
                'create_table' => 'CREATE TABLE migration_history ...',
                'track_applied' => 'Record each migration application'
            ]
        ]
    ],
    'backup_procedures' => [
        'before_migration' => [
            '1' => 'Full database backup',
            '2' => 'Test migration on staging',
            '3' => 'Document rollback procedure'
        ],
        'automated_backups' => [
            'daily' => 'Automated daily database dumps',
            'retention' => 'Keep last 7 daily backups',
            'location' => 'Store in /private/backups/'
        ]
    ]
];

$continuous_integration = [
    'github_actions' => [
        'setup' => [
            'workflow_file' => '.github/workflows/main.yml',
            'triggers' => [
                'on_push' => 'Run tests on every push',
                'on_pull_request' => 'Run tests on PR creation/update'
            ]
        ],
        'actions' => [
            'lint' => 'Check PHP syntax',
            'test' => 'Run PHPUnit tests',
            'security' => 'Run security scanners'
        ]
    ],
    'staging_deployment' => [
        'workflow' => [
            '1' => 'Push to staging branch',
            '2' => 'Automated tests run',
            '3' => 'Deploy to staging server',
            '4' => 'Run integration tests'
        ],
        'monitoring' => [
            'logs' => 'Aggregate logs for review',
            'metrics' => 'Track deployment success rate',
            'alerts' => 'Notify team of failures'
        ]
    ]
];

$template_distribution_workflow = [
    'concept_overview' => [
        'master_template' => 'Your XAMPP workspace is the "master template"',
        'site_repositories' => 'Each cPanel site gets its own Git repository',
        'sync_workflow' => 'Master template → individual sites → production deployment',
        'customization_tracking' => 'Each site can have custom modifications while staying synced'
    ],
    'step_1_prepare_master_template' => [
        'action' => 'Clean up master template for distribution',
        'instructions' => [
            '1' => 'Complete canonical-plan.php cleanup successfully',
            '2' => 'Test all functionality thoroughly',
            '3' => 'Remove any site-specific configuration',
            '4' => 'Update documentation',
            '5' => 'Commit final template: git commit -m "Master template v1.0 ready for distribution"',
            '6' => 'Create release tag: git tag template-v1.0'
        ]
    ],
    'step_2_create_site_specific_copies' => [
        'for_each_new_site' => [
            '1' => 'Create new folder: c:\\xampp\\htdocs\\client-site-name',
            '2' => 'Copy entire template: xcopy /E /I gws-universal-hybrid-app client-site-name',
            '3' => 'Navigate to new folder: cd c:\\xampp\\htdocs\\client-site-name',
            '4' => 'Initialize new repository: git init',
            '5' => 'Add all files: git add .',
            '6' => 'Initial commit: git commit -m "Initial site setup from template v1.0"',
            '7' => 'Create branch for customizations: git checkout -b site-customizations'
        ],
        'folder_structure' => [
            'c:\\xampp\\htdocs\\gws-universal-hybrid-app' => 'Master template',
            'c:\\xampp\\htdocs\\client-site-1' => 'Site 1 customizations',
            'c:\\xampp\\htdocs\\client-site-2' => 'Site 2 customizations',
            'c:\\xampp\\htdocs\\client-site-3' => 'Site 3 customizations'
        ]
    ],
    'step_3_customization_workflow' => [
        'making_site_specific_changes' => [
            '1' => 'Work in site-specific folder: cd c:\\xampp\\htdocs\\client-site-1',
            '2' => 'Make sure you\'re on customization branch: git checkout site-customizations',
            '3' => 'Make your changes (branding, colors, content, etc.)',
            '4' => 'Test changes in XAMPP',
            '5' => 'Commit changes: git commit -m "Added client branding and custom colors"',
            '6' => 'Continue iterating until site is ready'
        ],
        'tracking_what_changed' => [
            'view_differences' => 'git diff main site-customizations',
            'what_this_shows' => 'Exactly what\'s different from master template',
            'why_important' => 'Know what to preserve when updating template'
        ]
    ]
];

// ============================================================================
// SECTION 10: TROUBLESHOOTING MULTI-SITE WORKFLOWS
// ============================================================================

$multi_site_troubleshooting = [
    'connection_issues' => [
        'cannot_connect_to_cpanel' => [
            'symptoms' => 'git push fails with "Permission denied" or "Connection refused"',
            'solutions' => [
                '1' => 'Verify SSH keys are set up correctly',
                '2' => 'Check if hosting provider supports Git over SSH',
                '3' => 'Try HTTPS authentication instead of SSH',
                '4' => 'Verify correct username and server address',
                '5' => 'Contact hosting provider for Git access requirements'
            ]
        ],
        'wrong_remote_urls' => [
            'symptoms' => 'Push succeeds but files don\'t appear on website',
            'solutions' => [
                '1' => 'Verify remote URL points to public_html directory',
                '2' => 'Check git remote -v output for correct paths',
                '3' => 'SSH into cPanel and verify Git repository location',
                '4' => 'Update remote URL: git remote set-url production [correct-url]'
            ]
        ]
    ],
    'deployment_conflicts' => [
        'merge_conflicts_on_push' => [
            'symptoms' => 'Git says "failed to push" with merge conflict messages',
            'solutions' => [
                '1' => 'Pull from remote first: git pull production main',
                '2' => 'Resolve any conflicts in VS Code',
                '3' => 'Commit resolved conflicts',
                '4' => 'Push again: git push production main'
            ]
        ],
        'files_not_updating' => [
            'symptoms' => 'Push succeeds but website shows old content',
            'solutions' => [
                '1' => 'SSH into cPanel: git status (check if files are staged)',
                '2' => 'Force checkout: git checkout main --force',
                '3' => 'Clear cPanel file cache if available',
                '4' => 'Check file permissions (should be 644 for files, 755 for directories)'
            ]
        ]
    ],
    'workspace_confusion' => [
        'working_in_wrong_workspace' => [
            'symptoms' => 'Changes intended for one client appear in another',
            'prevention' => [
                '1' => 'Always check VS Code window title before working',
                '2' => 'Use git branch to verify current branch',
                '3' => 'Set up different VS Code color themes per workspace',
                '4' => 'Create workspace-specific terminal titles'
            ]
        ],
        'mixed_up_remotes' => [
            'symptoms' => 'Pushed client A\'s changes to client B\'s server',
            'emergency_response' => [
                '1' => 'Immediately rollback affected site: git reset --hard previous-commit',
                '2' => 'Verify correct workspace and remote: git remote -v',
                '3' => 'Push correct content to correct site',
                '4' => 'Set up better naming conventions to prevent recurrence'
            ]
        ]
    ]
];

// ============================================================================
// SECTION 11: COMPLETE SETUP CHECKLIST FOR RIGGSZOO.COM
// ============================================================================

$riggszoo_complete_checklist = [
    'pre_setup_requirements' => [
        'software_installed' => [
            '✓ Git for Windows installed and configured',
            '✓ VS Code installed with Git extensions',
            '✓ XAMPP running with GWS workspace',
            '✓ Canonical-plan.php cleanup completed successfully'
        ],
        'riggszoo_access' => [
            '✓ riggszoo.com cPanel login credentials',
            '✓ riggszoo.com domain DNS configured',
            '✓ SSL certificate active on riggszoo.com',
            '✓ Email accounts set up if needed'
        ]
    ],
    'step_by_step_execution' => [
        'phase_1_local_preparation' => [
            '1' => 'Open VS Code with GWS workspace',
            '2' => 'Verify all changes committed: git status',
            '3' => 'Create riggszoo tag: git tag riggszoo-baseline',
            '4' => 'Update config files for riggszoo.com domain',
            '5' => 'Test all functionality in XAMPP one final time'
        ],
        'phase_2_cpanel_setup' => [
            '1' => 'Log into riggszoo.com cPanel',
            '2' => 'Backup existing files (if any)',
            '3' => 'Create MySQL database: riggszoo_gws',
            '4' => 'Create database user with full privileges',
            '5' => 'Set up Git repository in public_html',
            '6' => 'Note Git clone URL for connection'
        ],
        'phase_3_connection' => [
            '1' => 'Add riggszoo remote: git remote add riggszoo [clone-url]',
            '2' => 'Test connection: git ls-remote riggszoo',
            '3' => 'If connection fails, troubleshoot SSH/HTTPS setup',
            '4' => 'Verify connection: git remote -v'
        ],
        'phase_4_deployment' => [
            '1' => 'Push to riggszoo: git push riggszoo main',
            '2' => 'SSH into riggszoo or use cPanel terminal',
            '3' => 'Verify files deployed: ls -la public_html',
            '4' => 'Import database from XAMPP export',
            '5' => 'Update config file with production database credentials'
        ],
        'phase_5_testing' => [
            '1' => 'Visit riggszoo.com - verify homepage loads',
            '2' => 'Test admin login: riggszoo.com/admin/',
            '3' => 'Verify all admin functions work',
            '4' => 'Test contact forms and email sending',
            '5' => 'Check all navigation and internal links'
        ]
    ],
    'success_criteria' => [
        'riggszoo.com loads correctly',
        'Admin area fully functional',
        'Database connections working',
        'Email systems operational',
        'Git push/pull working between XAMPP and riggszoo.com',
        'Template ready for client demonstrations'
    ]
];

// ============================================================================
// SECTION 12: STREAMLINED FUTURE CLIENT SETUP PROCESS
// ============================================================================

$future_client_setup_process = [
    'assumptions' => [
        'vs_code_configured' => 'VS Code + Git already set up from riggszoo setup',
        'riggszoo_working' => 'riggszoo.com is working as template source',
        'git_experience' => 'You\'ve successfully completed riggszoo.com setup'
    ],
    'new_client_workflow' => [
        'step_1_workspace_creation' => [
            'time_estimate' => '5 minutes',
            'actions' => [
                '1' => 'cd C:\\xampp\\htdocs',
                '2' => 'xcopy /E /I gws-universal-hybrid-app client-newcompany',
                '3' => 'cd client-newcompany',
                '4' => 'git init',
                '5' => 'git add . && git commit -m "Initial setup from template"'
            ]
        ],
        'step_2_customization' => [
            'time_estimate' => '2-8 hours (depending on customizations)',
            'areas_to_customize' => [
                'branding' => 'Logo, colors, fonts, favicon',
                'content' => 'Homepage, about, services, contact info',
                'configuration' => 'Site title, SEO, email settings'
            ],
            'commit_frequently' => 'git commit after each major change'
        ],
        'step_3_cpanel_setup' => [
            'time_estimate' => '10-15 minutes',
            'actions' => [
                '1' => 'Log into client cPanel',
                '2' => 'Create database and user',
                '3' => 'Set up Git repository',
                '4' => 'Configure email if needed'
            ]
        ],
        'step_4_connection_and_deployment' => [
            'time_estimate' => '10-15 minutes',
            'actions' => [
                '1' => 'git remote add production [client-cpanel-url]',
                '2' => 'Update config for production database',
                '3' => 'git push production main',
                '4' => 'Import database to production',
                '5' => 'Test live site functionality'
            ]
        ]
    ],
    'total_time_per_new_client' => [
        'first_client_after_riggszoo' => '4-10 hours (including learning)',
        'subsequent_clients' => '2-4 hours (streamlined process)',
        'maintenance_per_site' => '30 minutes per month average'
    ]
];

// ============================================================================
// SECTION 13: PROFESSIONAL WORKFLOW OPTIMIZATION
// ============================================================================

$professional_workflow_optimization = [
    'vs_code_workspace_management' => [
        'multi_root_workspaces' => [
            'concept' => 'Open multiple client folders in single VS Code window',
            'setup' => [
                '1' => 'File → Add Folder to Workspace',
                '2' => 'Add each client folder to same workspace',
                '3' => 'File → Save Workspace As → "All-Clients.code-workspace"',
                '4' => 'Switch between projects in same window'
            ],
            'benefits' => [
                'compare_files' => 'Compare files between client sites',
                'global_search' => 'Search across all client projects',
                'unified_git' => 'See Git status for all projects at once'
            ]
        ],
        'color_coding_workspaces' => [
            'different_themes_per_client' => [
                'template_workspace' => 'Dark+ (default)',
                'client_a_workspace' => 'Light+ theme',
                'client_b_workspace' => 'Monokai theme'
            ],
            'setup' => 'Install "Peacock" extension for workspace color coding'
        ]
    ],
    'automated_deployment_scripts' => [
        'batch_deployment' => [
            'concept' => 'Deploy to multiple client sites with single command',
            'implementation' => 'Create PowerShell scripts for common operations',
            'example_script' => [
                'deploy-all-clients.ps1' => 'Push updates to all production sites',
                'backup-all-sites.ps1' => 'Create backups before major updates',
                'test-all-sites.ps1' => 'Verify all sites are functioning'
            ]
        ]
    ],
    'client_communication_integration' => [
        'git_commit_messages_for_clients' => [
            'format' => 'git commit -m "CLIENT: Added new contact form validation"',
            'benefits' => 'Easy to generate client reports from Git history',
            'reporting' => 'git log --grep="CLIENT" --oneline for client updates'
        ],
        'staging_site_workflow' => [
            'concept' => 'Show clients changes on staging before production',
            'workflow' => 'XAMPP → staging → client approval → production',
            'commands' => [
                'git push staging main' => 'Deploy to staging for client review',
                'git push production main' => 'Deploy to production after approval'
            ]
        ]
    ]
];

$xampp_to_cpanel_workflow = [
    'deployment_overview' => [
        'development_phase' => 'Work in XAMPP (safe, local environment)',
        'testing_phase' => 'Test thoroughly in local environment',
        'staging_phase' => 'Optional: test on staging server',
        'production_phase' => 'Deploy to live cPanel site',
        'monitoring_phase' => 'Watch for issues, be ready to rollback'
    ],
    'step_1_prepare_for_deployment' => [
        'pre_deployment_checklist' => [
            '1' => 'All changes committed in Git',
            '2' => 'All functionality tested in XAMPP',
            '3' => 'Database changes documented',
            '4' => 'Configuration files updated for production',
            '5' => 'Backup current production site',
            '6' => 'Create deployment tag: git tag deployment-2025-08-09'
        ]
    ],
    'step_2_cpanel_git_setup' => [
        'action' => 'Set up Git repository on cPanel hosting',
        'instructions' => [
            '1' => 'Log into your cPanel account',
            '2' => 'Find "Git Version Control" in cPanel (location varies by host)',
            '3' => 'Click "Create Repository"',
            '4' => 'Choose public_html folder (or subdomain folder)',
            '5' => 'Initialize repository',
            '6' => 'Note the Git URL provided by cPanel'
        ],
        'alternative_method' => [
            'ssh_access' => 'If your host provides SSH access:',
            '1' => 'SSH into your hosting account',
            '2' => 'cd public_html',
            '3' => 'git init',
            '4' => 'Set up remote connection to your local repository'
        ]
    ],
    'step_3_connect_local_to_cpanel' => [
        'action' => 'Link your XAMPP repository to cPanel',
        'instructions' => [
            '1' => 'In XAMPP command prompt: cd c:\\xampp\\htdocs\\client-site-1',
            '2' => 'Add cPanel as remote: git remote add production [cpanel-git-url]',
            '3' => 'Verify connection: git remote -v',
            '4' => 'You should see "production" pointing to your cPanel URL'
        ],
        'example_remote_url' => 'git remote add production ssh://user@yoursite.com/public_html/.git'
    ],
    'step_4_deployment_process' => [
        'safe_deployment_steps' => [
            '1' => 'Final local test: verify everything works in XAMPP',
            '2' => 'Commit final changes: git commit -m "Ready for production deployment"',
            '3' => 'Push to production: git push production main',
            '4' => 'SSH into cPanel and run: git checkout main (if needed)',
            '5' => 'Test production site immediately',
            '6' => 'Monitor for 10-15 minutes for any issues'
        ],
        'what_git_push_does' => 'Copies your local changes to the production server',
        'immediate_testing' => 'Load your live site and test critical functionality'
    ],
    'step_5_emergency_rollback' => [
        'if_production_breaks' => [
            'immediate_action' => 'Don\'t panic - you have complete rollback capability',
            'rollback_steps' => [
                '1' => 'SSH into your cPanel',
                '2' => 'cd public_html (or your site folder)',
                '3' => 'Find last working version: git log --oneline',
                '4' => 'Rollback: git reset --hard [last-working-commit]',
                '5' => 'Test site - should be working again'
            ],
            'example' => 'git reset --hard deployment-2025-08-08'
        ],
        'prevention' => 'Always test in XAMPP first, deploy during low-traffic times'
    ]
];

// ============================================================================
// SECTION 14: COMMON BEGINNER PROBLEMS AND SOLUTIONS
// ============================================================================

$common_problems_and_solutions = [
    'git_not_recognized' => [
        'problem' => 'Command prompt says "git is not recognized"',
        'cause' => 'Git not installed correctly or not in PATH',
        'solution' => [
            '1' => 'Reinstall Git with correct PATH setting',
            '2' => 'Choose "Git from command line and 3rd-party software" during install',
            '3' => 'Restart Command Prompt after installation',
            '4' => 'If still not working, manually add Git to PATH environment variable'
        ]
    ],
    'permission_denied' => [
        'problem' => 'Permission denied when trying to push to cPanel',
        'cause' => 'SSH keys not set up or incorrect permissions',
        'solution' => [
            '1' => 'Set up SSH keys between local machine and cPanel',
            '2' => 'Or use HTTPS authentication with username/password',
            '3' => 'Check with hosting provider for correct Git URL format'
        ]
    ],
    'merge_conflicts' => [
        'problem' => 'Git says there are "merge conflicts"',
        'cause' => 'Same file was changed in two different places',
        'solution' => [
            '1' => 'Don\'t panic - this is normal and fixable',
            '2' => 'Open conflicted files in text editor',
            '3' => 'Look for <<<<<<< ======= >>>>>>> markers',
            '4' => 'Choose which version to keep',
            '5' => 'Remove the conflict markers',
            '6' => 'git add . && git commit -m "Resolved merge conflict"'
        ]
    ],
    'lost_changes' => [
        'problem' => 'Made changes but can\'t find them',
        'cause' => 'Changes not committed or working in wrong branch',
        'solution' => [
            '1' => 'Check status: git status',
            '2' => 'Check current branch: git branch',
            '3' => 'Check recent commits: git log --oneline',
            '4' => 'If changes not committed: git add . && git commit -m "Save my changes"'
        ]
    ],
    'wrong_folder' => [
        'problem' => 'Git commands not working',
        'cause' => 'Not in a Git repository folder',
        'solution' => [
            '1' => 'Check current location: dir (Windows) or pwd (Linux)',
            '2' => 'Navigate to correct folder: cd c:\\xampp\\htdocs\\gws-universal-hybrid-app',
            '3' => 'Verify Git repo exists: dir .git (should show .git folder)'
        ]
    ]
];

// ============================================================================
// SECTION 10: AI-ASSISTED EXECUTION PLAN
// ============================================================================

$ai_assisted_execution = [
    'how_to_ask_ai_for_help' => [
        'effective_requests' => [
            '"Walk me through setting up Git step by step"',
            '"I got an error when trying to commit, help me fix it"',
            '"Show me how to deploy my XAMPP site to cPanel using Git"',
            '"I need to rollback my production site, what commands do I use?"'
        ],
        'information_to_provide' => [
            'what_step_youre_on' => 'I\'m on step 3 of the Windows installation',
            'exact_error_message' => 'The error says: "Permission denied (publickey)"',
            'what_you_were_trying' => 'I was trying to push to my cPanel server',
            'your_setup' => 'I\'m using Windows 11 with XAMPP and cPanel hosting'
        ]
    ],
    'ai_can_help_with' => [
        'command_explanation' => 'What does this Git command do?',
        'error_troubleshooting' => 'How do I fix this specific error?',
        'workflow_guidance' => 'What should I do next in my deployment?',
        'best_practices' => 'Am I doing this the right way?'
    ],
    'ai_limitations' => [
        'cannot_access_your_computer' => 'AI can\'t run commands for you',
        'cannot_see_your_screen' => 'AI can\'t see what you\'re seeing',
        'cannot_fix_server_issues' => 'AI can\'t log into your hosting account',
        'but_can_guide_you' => 'AI can give you exact commands to run and explain everything'
    ]
];

// ============================================================================
// SECTION 11: IMPLEMENTATION TIMELINE FOR BEGINNERS
// ============================================================================

$beginner_implementation_timeline = [
    'session_1_basic_setup' => [
        'time_needed' => '30-45 minutes',
        'goals' => [
            'Install Git on Windows',
            'Configure your name and email',
            'Initialize your first repository',
            'Make your first commit'
        ],
        'success_criteria' => 'You can run git status and see "working tree clean"'
    ],
    'session_2_backup_before_cleanup' => [
        'time_needed' => '15-20 minutes',
        'goals' => [
            'Create baseline backup',
            'Set up safety tags',
            'Prepare for canonical-plan.php execution'
        ],
        'success_criteria' => 'You have a tagged backup you can return to'
    ],
    'session_3_template_distribution' => [
        'time_needed' => '45-60 minutes',
        'goals' => [
            'Create site-specific copies',
            'Set up customization workflow',
            'Practice making and tracking changes'
        ],
        'success_criteria' => 'You have multiple site folders with independent Git tracking'
    ],
    'session_4_cpanel_deployment' => [
        'time_needed' => '60-90 minutes',
        'goals' => [
            'Set up cPanel Git repository',
            'Connect local to production',
            'Practice deployment and rollback'
        ],
        'success_criteria' => 'You can deploy changes and rollback if needed'
    ]
];

// ============================================================================
// FINAL NOTES AND ENCOURAGEMENT
// ============================================================================

// Ensure all arrays are terminated and accessible
$git_workflow_guide = [
    'basic_concepts' => $why_git_is_essential,
    'setup' => [
        'vscode_integration' => $vscode_git_integration,
        'windows_installation' => $windows_git_installation,
        'initial_config' => $first_time_configuration
    ],
    'workflow' => [
        'repository_setup' => $first_repository_setup,
        'daily_commands' => $essential_daily_commands,
        'backup_strategy' => $backup_strategy_for_canonical_plan
    ],
    'deployment' => [
        'template_setup' => $riggszoo_template_setup,
        'workspace_setup' => $future_workspace_setup,
        'site_management' => $multi_site_management
    ],
    'advanced_topics' => [
        'distribution' => $template_distribution_workflow,
        'conflict_handling' => $merge_conflict_management,
        'branching' => $branching_strategies,
        'database' => $database_version_control,
        'ci_cd' => $continuous_integration
    ]
];

echo "
/*
 * CONGRATULATIONS ON TAKING THIS STEP!
 * 
 * Git might seem overwhelming at first, but you're learning a skill that will:
 * - Save you countless hours of lost work
 * - Give you confidence to experiment and try new things
 * - Make you a more professional developer
 * - Enable you to manage multiple sites efficiently
 * 
 * Remember:
 * - Start small - just get Git installed and make your first commit
 * - Practice with non-critical files first
 * - Don't be afraid to ask AI for help with specific commands
 * - Every professional developer went through this learning curve
 * 
 * You've got this! Take it one step at a time.
 */
";

// ============================================================================
// SECTION X: IMPORTANT - SSH PORT AND HOSTNAME FOR CPANEL GIT DEPLOYMENT
// ============================================================================

/*
 * When connecting your local Git repository to cPanel using SSH, you MUST know:
 * 
 * 1. The correct SSH hostname (this is often NOT your website domain, but a server name like 'server123.hostingcompany.com').
 * 2. The correct SSH port (default is 22, but many hosts use a custom port for security).
 * 
 * HOW TO FIND THIS INFORMATION:
 * - Check your hosting provider's documentation or knowledge base for "SSH access" or "SSH port".
 * - Look for a "Connection Information" or "Instructions" section in cPanel's SSH Access page.
 * - Review your hosting welcome email for SSH details.
 * - If you cannot find the port or hostname, contact your hosting provider's support and ask:
 *      "What is the SSH hostname and port for my cPanel account?"
 * 
 * When running SSH or Git commands, always specify the port if it's not 22:
 * 
 *   ssh -i "C:\Users\YourName\.ssh\your-key" -p PORT_NUMBER username@hostname
 * 
 *   GIT_SSH_COMMAND="ssh -i 'C:/Users/YourName/.ssh/your-key' -p PORT_NUMBER" git push -u cpanel main
 * 
 * Replace PORT_NUMBER, username, hostname, and key path as needed.
 * 
 * If your Windows username or any folder in the path contains spaces, wrap the path in double quotes.
 * 
 * Example:
 *   ssh -i "C:\Users\Not Donna\.ssh\gws-cpanel" -p 2222 riggszoo@server123.hostingcompany.com
 * 
 * If you use the wrong hostname or port, you will get connection errors or timeouts.
 */

/*
 * HOW TO CREATE A GIT REPOSITORY IN CPANEL FOR NEW WEBSITES
 * 
 * For each new cPanel website, follow these steps to set up a Git repository in the correct location:
 * 
 * 1. Log in to the new website's cPanel account.
 * 2. Go to "Git Version Control" in the cPanel dashboard.
 * 3. Click "Create" or "Create Repository".
 * 4. For the Repository Path, enter:
 *      /home/[new_cpanel_username]/public_html
 *    (Replace [new_cpanel_username] with the actual cPanel username for the site.)
 * 5. For the Repository Name, enter:
 *      git_repository_public
 * 6. Leave "Clone a Repository" OFF (do not enable).
 * 7. Click "Create".
 * 8. After creation, cPanel will show you the repository details and the clone URL.
 * 9. Use this clone URL to connect your local Git repository to the cPanel site.
 * 
 * Example for a new cPanel user "client123":
 *      Repository Path: /home/client123/public_html
 *      Repository Name: git_repository_public
 * 
 * This ensures your website files are tracked by Git and deployed directly to the live site (not in a subfolder).
 * 
 * For each new client or site, repeat these steps with their specific cPanel username.
 */

// ============================================================================\n// SECTION: USING THIS WORKSPACE AS A TEMPLATE FOR NEW PROJECTS AND CPANEL SITES\n// ============================================================================\n/*\n * QUICK TEMPLATE-TO-CLIENT WORKFLOW\n *\n * 1. Copy this workspace for your new project:\n *    cd C:\\xampp\\htdocs\n *    xcopy /E /I gws-universal-hybrid-app client-newproject\n *    cd client-newproject\\public_html\n *\n * 2. Initialize Git and connect to GitHub:\n *    git init\n *    git add .\n *    git commit -m "Initial commit"\n *    git branch -M main\n *    # Create a new GitHub repo (private or public)\n *    git remote add origin https://github.com/yourusername/client-newproject.git\n *    git push -u origin main\n *\n * 3. Set up cPanel Git repo for the new site (see instructions above).\n *\n * 4. Connect local to cPanel:\n *    git remote add production [cpanel-clone-url]\n *    git push production main\n *\n * 5. After each local change:\n *    git add .\n *    git commit -m "Describe your change"\n *    git push origin main\n *    git push production main\n *    # In cPanel, use "Pull" in Git Version Control if needed.\n *\n * 6. For each new client/project, repeat steps 1–5.\n *    - Customize branding, content, and config for each client before pushing.\n *\n * Best Practices:\n * - Always work in the correct project folder.\n * - Use .gitignore to protect sensitive files (e.g., /private/openai-key.php).\n * - Use clear commit messages.\n * - Tag releases for major deployments.\n * - For multi-site management, keep a "template" remote and a "production" remote.\n */

/*
 * SECTION: BEST PRACTICES FOR REPOSITORY STRUCTURE (PUBLIC, PRIVATE, ADMIN, PORTAL)
 * ============================================================================

 * 1. Separate Repositories for public_html and private
 *    - Use one repository for all code in public_html (your website's public files).
 *    - Use a separate repository for your private folder (e.g., /home/youruser/private) if you want version control for sensitive or backend-only code.
 *    - This keeps public and private codebases isolated and secure.
 *
 * 2. Admin and client_portal as Subdirectories of public_html

// ============================================================================
// SECTION: CPANEL & PRIVATE GITHUB REPO LIMITATIONS AND WORKAROUNDS
// ============================================================================
/*
 * IMPORTANT: cPanel and Private GitHub Repositories
 *
 * Many cPanel hosts cannot authenticate with private GitHub repos using the Git Version Control UI.
 * This means you may see errors or be unable to pull from your private repo unless you use one of these methods:
 *
 * 1. Preferred: Use a Deploy Key or Personal Access Token (PAT)
 *    - In GitHub, go to your repo Settings > Deploy keys. Add a new SSH key (public key from cPanel or your server).
 *    - Or, create a Personal Access Token (PAT) in GitHub (with repo access) and use it as the password when cPanel prompts for credentials.
 *    - If your cPanel host supports this, you can keep your repo private and pull securely.
 *    - See: https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token
 *
 * 2. If deploy keys/PAT are NOT supported by your cPanel host:
 *    - Temporarily set your GitHub repo to public before pulling from cPanel.
 *    - After updating your site, immediately set the repo back to private in GitHub.
 *    - WARNING: While public, your code is visible to anyone. Only do this briefly and never leave sensitive data in the repo.
 *
 * 3. Alternative: Use SSH access (if available)
 *    - If your host allows SSH, you can manually pull from your private repo using your SSH key (added to GitHub).
 *    - This is more advanced but avoids making your repo public.
 *
 * Summary:
 * - Always prefer deploy keys or PAT for private repo access.
 * - If not possible, document the need to temporarily make the repo public and set a reminder to revert it.
 * - Never store secrets or sensitive data in your repo, public or private.
 *
 * Document this process for all future deployments to avoid confusion and security risks.
 */

/*
 * REPOSITORY STRUCTURE BEST PRACTICES:
 * - If your admin and client_portal are subfolders of public_html, you generally do NOT need separate repositories for each.
 * - One repository for public_html is usually best, as it allows you to track and deploy the entire website (including admin and client_portal) together.
 * - Only use separate repositories for admin or client_portal if:
 *     a) They are developed/deployed by completely separate teams,
 *     b) You want to restrict access to their codebases at the Git level,
 *     c) You have a very large project and want to decouple deployments.
 * - For most small/medium projects, a single public_html repo is simpler and less error-prone.
 *
 * REPOSITORY STRUCTURE SUMMARY:
 * | Folder/Area            | Recommended Repo Setup         |
 * |------------------------|-------------------------------|
 * | public_html            | 1 repo (includes admin/portal) |
 * | private                | 1 separate repo                |
 * | admin (subdir)         | included in public_html repo   |
 * | client_portal (subdir) | included in public_html repo   |
 *
 * DEPLOYMENT CONSIDERATIONS:
 * - public_html repo deploys to your live website.
 * - private repo is managed separately and not exposed to the web.
 *
 * ADVANCED OPTIONS:
 * - If you need to split admin or client_portal into their own repos later,
 *   you can use Git submodules or subtree merges, but this adds complexity.
 *
 * SUMMARY:
 * For most projects, use TWO repositories—one for public_html (including admin
 * and client_portal) and one for private.
 */

// ============================================================================
// SECTION: REMOVING SENSITIVE FILES FROM GIT HISTORY (BFG + JAVA)
// ============================================================================
/*
 * If you accidentally commit a sensitive file (like openai-key.php) and GitHub blocks your push for security:
 *
 * 1. Install Java (required for BFG Repo-Cleaner):
 *    - Go to https://adoptium.net/ or https://www.oracle.com/java/technologies/downloads/
 *    - Download the latest MSI installer for Windows (easiest option).
 *    - Run the installer and follow the prompts.
 *    - After install, restart Command Prompt and check: java -version
 *      (Should show version 11, 17, or higher.)
 *
 * 2. Download BFG Repo-Cleaner:
 *    - https://rtyley.github.io/bfg-repo-cleaner/
 *    - Save the .jar file to your Downloads folder.
 *
 * 3. Make a backup of your repo (optional but recommended):
 *    cd C:\xampp\htdocs\gws-universal-hybrid-app
 *    xcopy /E /I public_html public_html-backup
 *
 * 4. Remove the sensitive file from git history:
 *    cd public_html
 *    java -jar "C:\Users\Your Name\Downloads\bfg-1.15.0.jar" --delete-files openai-key.php .
 *    (If your Windows username has spaces, keep the path in double quotes.)
 *
 * 5. Clean and update your repo:
 *    git reflog expire --expire=now --all
 *    git gc --prune=now --aggressive
 *
 * 6. Force-push the cleaned history to GitHub:
 *    git push --force origin main
 *
 * 7. Confirm on GitHub that the file and its contents are gone from all history.
 *
 * Always add sensitive files to .gitignore to prevent future issues!
 */