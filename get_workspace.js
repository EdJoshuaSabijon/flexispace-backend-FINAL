const token = '8019c0e1-1294-49b6-aeb8-db746ecc994b';

async function getWorkspace() {
  const query = `
    query {
      workspaces {
        edges {
          node {
            id
            name
          }
        }
      }
    }
  `;
  
  const response = await fetch('https://backboard.railway.app/graphql/v2', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ query })
  });
  
  const data = await response.json();
  console.log(JSON.stringify(data, null, 2));
}

getWorkspace();
