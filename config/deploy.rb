
# A Capistrano deployment for deploying this plug-in to a server 

set :application, "related_tags"
set :scm, "git"
set :repository, "git@github.com:geektastical/Related-Tags.git"
set :branch, "master"

set :use_sudo,	false

# Credentials and deployment location are kept outside 
# of the repositoy for security reasons. Create a 
# credential file with the following variables.
#
# Capistrano::Configuration.instance.load do
#  set :deploy_to, <deploy_location>
#  role :app, <server name or ip>
#  set :user, <deploy account username>
#  set :password, <deploy account password>
# end
#
#
require '../Credentials/related_tags.rb'

namespace :deploy do
 
		task :update do
			transaction do
				update_code
				symlink
			end
		end
 
		task :finalize_update do
			transaction do
				run "chmod -R g+w #{releases_path}/#{release_name}"
			end
		end
 
		task :symlink do
			transaction do
				run "ln -nfs #{current_release} #{deploy_to}/#{current_dir}"
			end
		end
 
		task :migrate do
			# ignore rails default
		end
 
		task :restart do
			# ignore rails default
		end
 
end