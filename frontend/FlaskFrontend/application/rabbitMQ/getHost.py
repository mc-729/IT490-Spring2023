import configparser

def get_host_info(extra=None):
    machine = configparser.ConfigParser()
    machine.read('host.ini')
    if extra is not None:
        for ini_file in extra:
            parsed = configparser.ConfigParser()
            parsed.read(ini_file)
            machine.read_dict(parsed)
    return machine._sections