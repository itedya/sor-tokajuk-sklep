<?php

require_once __DIR__ . "/../../tooling/autoload.php";

$id = $_GET['id'] ?? null;
if ($id === null) redirect_and_kill(config("app.url") . "/management/products.php");
if (!is_numeric($id)) redirect_and_kill(config("app.url") . "/management/products.php");
$id = intval($id);

$product = db_query_row(get_db_connection(), "SELECT * FROM products WHERE id = ?", [$id]);
if ($product === null) redirect_and_kill(config("app.url") . "/management/products.php");

if (!old_input_has("name")) old_input_add("name", $product['name']);
if (!old_input_has("description")) old_input_add("description", $product['description']);

echo render_in_layout(function () use ($id) { ?>
    <div class="flex justify-center items-center p-4">
        <form method="POST" action="/auth/login.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
            <h1 class="text-4xl font-bold text-center text-neutral-300">Edytowanie produktu</h1>

            <img src="https://placehold.co/400x400" alt="Product image" class="w-full aspect-square rounded-xl"/>

            <div class="bg-neutral-800 border-4 border-neutral-800 relative rounded-xl">
                <label for="image" class="p-4 w-full flex flex-row gap-4 rounded-xl text-neutral-300 w-full h-full">Wybrano
                    zdjęcie: </label>
                <input type="file" name="image" id="image" class="w-full h-full absolute top-0 left-0 invisible"/>
            </div>

            <div class="flex flex-col gap-4">
                <?= render_textfield(label: 'Nazwa', name: 'name', type: 'text') ?>
                <?= render_textfield(label: 'Opis', name: 'description', type: 'textarea') ?>
                <?= render_textfield(label: 'Cena', name: 'price', type: 'number') ?>

                <div class="flex flex-col gap-4" id="parameter-elements-container">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row-reverse items-center justify-between gap-4">
                <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zapisz</button>
            </div>
        </form>
    </div>

    <template id="add-new-parameter-button">
        <div class="flex flex-row gap-4 w-full text-neutral-200 bg-neutral-800 rounded-xl p-4 cursor-pointer hover:bg-neutral-700 duration-300">
            <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
            Dodaj parametr
        </div>
    </template>

    <template id="select-new-parameter-select">
        <div class="flex flex-row gap-4 justify-center items-end">
            <?= render_select(label: "", name: "", options: [], validationError: false, oldInput: false) ?>

            <div class="add-button flex flex-row justify-center items-end gap-4 text-neutral-200 bg-neutral-800 rounded-xl p-4 cursor-pointer hover:bg-neutral-700 duration-300">
                <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
            </div>
        </div>
    </template>

    <template id="parameter-input">
        <div class="flex flex-row gap-4 justify-center items-end">
            <?= render_textfield(label: "", name: "", validationError: false, oldInput: false) ?>

            <div class="add-button flex flex-row justify-center items-end gap-4 text-neutral-200 bg-neutral-800 rounded-xl p-4 cursor-pointer hover:bg-neutral-700 duration-300">
                <?= file_get_contents(__DIR__ . "/../../assets/plus-icon.svg") ?>
            </div>
            <div class="remove-button flex flex-row justify-center items-end gap-4 text-neutral-200 bg-neutral-800 rounded-xl p-4 cursor-pointer hover:bg-neutral-700 duration-300">
                <?= file_get_contents(__DIR__ . "/../../assets/minus-icon.svg") ?>
            </div>
        </div>
    </template>

    <script>
        const allParameters = [
            {id: "kolor", name: "Kolor"},
            {id: "rozmiar", name: "Rozmiar"},
        ];

        const assignedParameters = [
            {id: "kolor", value: "Czerwony"},
        ];

        const elementsToRender = [];

        const parameterElementsContainer = document.getElementById("parameter-elements-container");

        const createAddNewParameterButton = () => {
            const template = document.getElementById("add-new-parameter-button").content.firstElementChild;
            const element = template.cloneNode(true);

            element.addEventListener("click", () => {
                elementsToRender.splice(elementsToRender.findIndex(element => element.type === "add-new-parameter-button"), 1);
                elementsToRender.push({type: "select-new-parameter-select", element: createSelectNewParameterSelect()});
                rerender();
            });

            return element;
        }

        const createSelectNewParameterSelect = () => {
            const template = document.getElementById("select-new-parameter-select").content.firstElementChild;
            const element = template.cloneNode(true);

            const select = element.querySelector("select");
            const options = allParameters.filter(parameter => {
                return assignedParameters.find(assignedParameter => assignedParameter.id === parameter.id) === undefined;
            }).map(parameter => ({
                value: parameter.id,
                text: parameter.name,
            }));

            options.push({value: "new-parameter*", text: "Dodaj nowy parametr"});
            options.unshift({value: "", text: "Wybierz..."});

            options.forEach(option => {
                const optionElement = document.createElement("option");
                optionElement.value = option.value;
                optionElement.innerText = option.text;

                select.appendChild(optionElement);
            });


            const addButton = element.querySelector(".add-button");

            addButton.addEventListener("click", () => {
                const parameterId = select.value;
                elementsToRender.splice(elementsToRender.findIndex(element => element.type === "select-new-parameter-select"), 1);

                if (parameterId === "new-parameter*") {
                    elementsToRender.push({
                        type: "new-parameter-input",
                        element: createNewParameterInput()
                    });
                } else {
                    const parameter = allParameters.find(parameter => parameter.id === parameterId);
                    assignedParameters.push({id: parameter.id, value: ""})

                    elementsToRender.push({
                        type: "parameter-input",
                        element: createParameterInput(parameter.id, parameter.name, "")
                    });
                    elementsToRender.push({type: "add-new-parameter-button", element: createAddNewParameterButton()})
                }

                rerender();
            });

            return element;
        }

        const createNewParameterInput = () => {
            const template = document.getElementById("parameter-input").content.firstElementChild;
            const element = template.cloneNode(true);

            const input = element.querySelector("input");
            input.removeAttribute("name");
            input.value = input.value.trim();

            const label = element.querySelector("label");
            label.innerText = "Nazwa nowego parametru";

            const addButton = element.querySelector(".add-button");

            addButton.style.display = "flex";

            addButton.addEventListener("click", () => {
                let parameterId = (() => {
                    const newParameterId = input.value.toLowerCase().replace(/\W/g, '');

                    if (allParameters.find(parameter => parameter.id === newParameterId) === undefined) return newParameterId;

                    let num = 1;
                    while (allParameters.find(parameter => parameter.id === newParameterId + num) !== undefined) {
                        num++;
                    }

                    return newParameterId;
                })();

                allParameters.push({id: parameterId, name: input.value});
                assignedParameters.push({id: parameterId, value: ""});

                elementsToRender.splice(elementsToRender.findIndex(element => element.element === element), 1);
                elementsToRender.push({
                    type: "parameter-input",
                    element: createParameterInput(parameterId, input.value, "")
                });
                elementsToRender.push({type: "add-new-parameter-button", element: createAddNewParameterButton()})
                rerender();
            });

            const removeButton = element.querySelector(".remove-button");

            removeButton.addEventListener("click", () => {
                elementsToRender.splice(elementsToRender.findIndex(e => e.element === element), 1);
                elementsToRender.push({type: "add-new-parameter-button", element: createAddNewParameterButton()});
                rerender();
            });

            return element;
        }

        const createParameterInput = (parameterId, parameterName, value = "") => {
            const template = document.getElementById("parameter-input").content.firstElementChild;
            const element = template.cloneNode(true);

            const input = element.querySelector("input");
            input.name = `parameter_${parameterId}`;
            input.value = value.trim();

            const label = element.querySelector("label");
            label.innerText = parameterName;

            const addButton = element.querySelector(".add-button");
            addButton.style.display = "none";

            const removeButton = element.querySelector(".remove-button");

            removeButton.addEventListener("click", () => {
                if (parameterId !== "new-parameter*") {
                    assignedParameters.splice(assignedParameters.findIndex(assignedParameter => assignedParameter.id === parameterId), 1);
                }
                elementsToRender.splice(elementsToRender.findIndex(e => e.element === element), 1);
                elementsToRender.splice(elementsToRender.findIndex(element => element.type === "select-new-parameter-select"), 1);
                elementsToRender.push({type: "select-new-parameter-select", element: createSelectNewParameterSelect()});
                rerender();
            });

            return element;
        }

        const rerender = () => {
            parameterElementsContainer.innerHTML = "";

            elementsToRender.forEach(element => {
                parameterElementsContainer.appendChild(element.element);
            });
        }

        assignedParameters.forEach(assignedParameter => {
            const parameterMetadata = allParameters.find(parameter => parameter.id === assignedParameter.id)

            elementsToRender.push({
                type: "parameter-input",
                element: createParameterInput(parameterMetadata.id, parameterMetadata.name, assignedParameter.value)
            });
        });

        elementsToRender.push({type: "add-new-parameter-button", element: createAddNewParameterButton()})
        rerender();
    </script>
<?php });