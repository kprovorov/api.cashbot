module.exports = {
    prompt: async ({prompter, args}) => {
        const module = await prompter
            .prompt({
                type: 'input',
                name: 'module',
                message: 'Specify module',
                initial: args.name
            })

        const fields = await prompter
            .prompt({
                type: 'list',
                name: 'fields',
                message: 'Specify fields',
            })
            .then(async ({fields}) => {

                    const fieldTypes = [];

                    for await (const field of fields) {

                        fieldTypes.push(await prompter.prompt({
                            type: 'select',
                            name: 'type',
                            message: `Choose type for field ${field}`,
                            choices: [
                                'string',
                                'int',
                                'float',
                                'timestamp',
                                'bool'
                            ]
                        }))
                    }

                    return fields.map((field, index) => {
                        return {
                            name: field,
                            type: fieldTypes[index].type
                        }
                    })
                }
            );

        return {
            ...module,
            fields
        };
    }
}
