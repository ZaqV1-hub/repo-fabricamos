const { spawn } = require('child_process');
const which = require('which');

const workflowName = process.argv[2];

if (!workflowName) {
	console.error('Please provide the workflow name.');
	process.exit(1);
}

console.warn(`\x1b[36mRunning workflow: ${workflowName}\x1b[0m`);

const actPath = which.sync('act');

const actProcess = spawn(
	actPath,
	[
		//NOSONAR
		'-W',
		`.github/workflows/${workflowName}.yml`,
		'--secret-file',
		'.secrets',
	],
	{
		shell: true,
		stdio: 'inherit', // Esto redirige stdin/stdout/stderr directamente a la consola
	}
);

actProcess.on('error', (error) => {
	console.error(`\x1b[33mError running workflow: ${error.message}\x1b[0m`);
	process.exit(1);
});

actProcess.on('close', (code) => {
	if (code === 0) {
		console.warn(`\x1b[32mWorkflow completed successfully\x1b[0m`);
	} else {
		console.error(`\x1b[33mWorkflow failed with exit code ${code}\x1b[0m`);
		process.exit(code);
	}
});
