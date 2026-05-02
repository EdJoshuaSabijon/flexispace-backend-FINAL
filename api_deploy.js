const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const token = '8019c0e1-1294-49b6-aeb8-db746ecc994b';
const projectId = '376e33db-127a-4eda-9524-8d6de2459650';

async function deployService(name, dir) {
  console.log(`Deploying ${name} from ${dir}...`);
  
  // 1. Create a tarball
  const tarPath = path.join(process.cwd(), `${name}.tar.gz`);
  // On Windows, we might need a specific command. Let's use 'tar' if available.
  try {
    execSync(`tar -czf "${tarPath}" -C "${dir}" . --exclude=node_modules --exclude=vendor --exclude=.git`);
  } catch (e) {
    console.error('Tar failed:', e.message);
    return;
  }

  // 2. Get upload URL
  const query = `
    mutation {
      deploymentCreate(input: { projectId: "${projectId}", serviceId: null }) {
        id
      }
    }
  `;
  // Actually, I need to create the service first if it doesn't exist.
  // But wait, Railway CLI is much better if it worked.
  
  console.log('Tarball created at', tarPath);
  console.log('Ready to upload...');
}

deployService('backend', 'd:\\8AM\\flexispace-backend');
